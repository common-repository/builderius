<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Applicant;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicant;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantInterface;
use Builderius\Bundle\TemplateBundle\Applicant\ParametersProvider\BuilderiusTemplateRuleApplicantParametersProviderInterface;
use Builderius\Bundle\TemplateBundle\Applicant\Provider\BuilderiusTemplateRuleApplicantsProviderInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplicantParametersAwareApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry\BuilderiusTemplateApplyRuleCategoriesRegistryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Bundle\TemplateBundle\ApplyRule\Converter\ApplyRuleConfigConverter;

class BuilderiusTemplateChangeSetApplicantsProvider implements BuilderiusTemplateChangeSetApplicantsProviderInterface
{
    /**
     * @var BuilderiusTemplateRuleApplicantsProviderInterface
     */
    private $applyRuleApplicantsProvider;

    /**
     * @var BuilderiusTemplateRuleApplicantParametersProviderInterface
     */
    private $applyRuleApplicantParametersProvider;

    /**
     * @var BuilderiusTemplateApplyRuleCategoriesRegistryInterface
     */
    private $applyRuleCategoriesRegistry;

    /**
     * @param BuilderiusTemplateRuleApplicantsProviderInterface $applyRuleApplicantsProvider
     * @param BuilderiusTemplateRuleApplicantParametersProviderInterface $applyRuleApplicantParametersProvider
     * @param BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry
     */
    public function __construct(BuilderiusTemplateRuleApplicantsProviderInterface $applyRuleApplicantsProvider, BuilderiusTemplateRuleApplicantParametersProviderInterface $applyRuleApplicantParametersProvider, BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry)
    {
        $this->applyRuleApplicantsProvider = $applyRuleApplicantsProvider;
        $this->applyRuleApplicantParametersProvider = $applyRuleApplicantParametersProvider;
        $this->applyRuleCategoriesRegistry = $applyRuleCategoriesRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getChangeSetApplicants(
        BuilderiusTemplateApplicantChangeSetInterface $changeset,
        array $applyRulesConfig = null
    ) {
        $applicants = [];
        $applicantParameters = [];
        if ($applyRulesConfig === null || empty($applyRulesConfig)) {
            return $applicants;
        }
        foreach ($applyRulesConfig['categories'] as $categoryName => $configSet) {
            $category = $this->applyRuleCategoriesRegistry->getCategory($categoryName);
            if ($category && $category instanceof BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface) {
                foreach (ApplyRuleConfigConverter::convert($configSet) as $key => $config) {
                    if (in_array($key, BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
                        $applicants = $this->checkApplicantGroupConfig($changeset, $config, $key);
                    } else {
                        $applicants = $this->checkApplicantSingleConfig($changeset, $config, $key);
                    }
                }
            }
        }
        foreach ($applyRulesConfig['categories'] as $categoryName => $configSet) {
            $category = $this->applyRuleCategoriesRegistry->getCategory($categoryName);
            if ($category && $category instanceof BuilderiusTemplateApplicantParametersAwareApplyRuleCategoryInterface) {
                foreach (ApplyRuleConfigConverter::convert($configSet) as $key => $config) {
                    if (in_array($key, BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
                        $applicantParameters = $this->checkApplicantParametersGroupConfig($config, $key);
                    } else {
                        $applicantParameters = $this->checkApplicantParametersSingleConfig($config, $key);
                    }
                }
            }
        }
        if (!empty($applicantParameters)) {
            $applicantsWithParams = [];
            foreach ($applicants as $k => $applicant) {
                $existingParams = $applicant->getParameters();
                foreach ($applicantParameters as $i => $applicantParameter) {
                    if (empty($existingParams)) {
                        $finalParams = $applicantParameter;
                    } else {
                        $finalParams = $existingParams;
                        foreach ($applicantParameter as $alias => $applicantParameterItems) {
                            if (isset($finalParams[$alias])) {
                                foreach ($applicantParameterItems as $applicantParameterItem) {
                                    if (!in_array($applicantParameterItem, $finalParams[$alias])) {
                                        $finalParams[$alias][] = $applicantParameterItem;
                                    }
                                }
                            } else {
                                $finalParams[$alias] = $applicantParameterItems;
                            }
                        }
                    }
                    $paramsString = http_build_query($finalParams);
                    $applicantsWithParams[sprintf('%s.%s', $k, $paramsString)] = new BuilderiusTemplateApplicant([
                        BuilderiusTemplateApplicant::URL_FIELD => $applicant->getUrl(),
                        BuilderiusTemplateApplicant::GROUP_LABEL_FIELD => $applicant->getGroupLabel() ?: $applicant->getLabel(),
                        BuilderiusTemplateApplicant::LABEL_FIELD => sprintf('Parameters Set #%d', $i),
                        BuilderiusTemplateApplicant::PARAMETERS_FIELD => $finalParams
                    ]);
                }
            }

            return $applicantsWithParams;
        } else {
            return $applicants;
        }
    }

    /**
     * @param BuilderiusTemplateApplicantChangeSetInterface $changeset
     * @param array $subConfigs
     * @param string $conjunction
     * @return BuilderiusTemplateApplicantInterface[]
     */
    private function checkApplicantGroupConfig(BuilderiusTemplateApplicantChangeSetInterface $changeset, array $subConfigs, $conjunction)
    {
        $applicantsSets = [];
        foreach ($subConfigs as $subConfig) {
            foreach ($subConfig as $key => $subSubConfig) {
                if (in_array($key, BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
                    $applicantsSets[] = $this->checkApplicantGroupConfig($changeset, $subSubConfig, $key);
                } else {
                    $applicantsSets[] = $this->checkApplicantSingleConfig($changeset, $subSubConfig, $key);
                }
            }
        }
        $applicants = [];
        if ($conjunction === 'and') {
            foreach ($applicantsSets as $key => $applicantsSet) {
                if ($key === 0) {
                    $applicants = $applicantsSet;
                } else {
                    $existingApplicants = $applicants;
                    $applicants = [];
                    foreach ($applicantsSet as $k => $applicant) {
                        if (in_array($applicant, $existingApplicants)) {
                            $applicants[$k] = $applicant;
                        }
                    }
                }
            }
        } else {
            foreach ($applicantsSets as $key => $applicantsSet) {
                if ($key === 0) {
                    $applicants = $applicantsSet;
                } else {
                    foreach ($applicantsSet as $k => $applicant) {
                        if (!in_array($applicant, $applicants)) {
                            $applicants[$k] = $applicant;
                        }
                    }
                }
            }
        }

        return $applicants;
    }

    /**
     * @param BuilderiusTemplateApplicantChangeSetInterface $changeset
     * @param array $config
     * @param string $operator
     * @return BuilderiusTemplateApplicantInterface[]
     */
    private function checkApplicantSingleConfig(BuilderiusTemplateApplicantChangeSetInterface $changeset, array $config, $operator)
    {
        $rule = $config[0]['var'];
        $argument = $config[1];

        return $this->applyRuleApplicantsProvider->getChangeSetApplicants($changeset, $rule, $argument, $operator);
    }

    /**
     * @param array $subConfigs
     * @param string $conjunction
     * @return array
     */
    private function checkApplicantParametersGroupConfig(array $subConfigs, $conjunction)
    {
        $applicantParametersSets = [];
        foreach ($subConfigs as $subConfig) {
            foreach ($subConfig as $key => $subSubConfig) {
                if (in_array($key, BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
                    $applicantParametersSets[] = $this->checkApplicantParametersGroupConfig($subSubConfig, $key);
                } else {
                    $applicantParametersSets[] = $this->checkApplicantParametersSingleConfig($subSubConfig, $key);
                }
            }
        }
        $parameters = [];
        if ($conjunction === 'and') {
            foreach ($applicantParametersSets as $key => $applicantParametersSet) {
                if ($key === 0) {
                    $parameters = $applicantParametersSet;
                } else {
                    foreach ($applicantParametersSet as $applicantParametersSubset) {
                        foreach ($applicantParametersSubset as $alias => $applicantAliasParameters) {
                            foreach ($parameters as $key => $parameterSubset) {
                                if (array_key_exists($alias, $parameterSubset)) {
                                    foreach ($applicantAliasParameters as $applicantAliasParameter) {
                                        if (!in_array($applicantAliasParameter, $parameterSubset[$alias])) {
                                            $parameters[$key][$alias][] = $applicantAliasParameter;
                                        }
                                    }
                                } else {
                                    foreach ($applicantAliasParameters as $applicantAliasParameter) {
                                        $parameters[$key][$alias][] = $applicantAliasParameter;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($applicantParametersSets as $applicantParametersSet) {
                foreach ($applicantParametersSet as $applicantParametersSubset) {
                    if (!in_array($applicantParametersSubset, $parameters)) {
                        $parameters[] = $applicantParametersSubset;
                    }
                }
            }
        }

        return $parameters;
    }

    /**
     * @param array $config
     * @param string $operator
     * @return array
     */
    private function checkApplicantParametersSingleConfig(array $config, $operator)
    {
        $rule = $config[0]['var'];
        $argument = $config[1];

        return $this->applyRuleApplicantParametersProvider->getApplicantParameters($rule, $argument, $operator);
    }
}