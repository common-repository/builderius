<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Applicant;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicant;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantInterface;
use Builderius\Bundle\TemplateBundle\Applicant\ParametersProvider\BuilderiusTemplateRuleApplicantParametersProviderInterface;
use Builderius\Bundle\TemplateBundle\Applicant\Provider\BuilderiusTemplateRuleApplicantsProviderInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplicantParametersAwareApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry\BuilderiusTemplateApplyRuleCategoriesRegistryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Bundle\TemplateBundle\ApplyRule\Converter\ApplyRuleConfigConverter;
use Builderius\Bundle\TemplateBundle\Event\ApplicantSingleConfigEvent;
use Builderius\Bundle\TemplateBundle\Hook\AbstractTemplateApplicantsCacheUpdateHook;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class BuilderiusTemplateApplicantsProvider implements BuilderiusTemplateApplicantsProviderInterface
{
    const CACHE_KEY = 'builderius_template_applicants';

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
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $applyRulesConfig;

    /**
     * @param BuilderiusTemplateRuleApplicantsProviderInterface $applyRuleApplicantsProvider
     * @param BuilderiusTemplateRuleApplicantParametersProviderInterface $applyRuleApplicantParametersProvider
     * @param BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        BuilderiusTemplateRuleApplicantsProviderInterface $applyRuleApplicantsProvider,
        BuilderiusTemplateRuleApplicantParametersProviderInterface $applyRuleApplicantParametersProvider,
        BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry,
        EventDispatcher $eventDispatcher
    ) {
        $this->applyRuleApplicantsProvider = $applyRuleApplicantsProvider;
        $this->applyRuleApplicantParametersProvider = $applyRuleApplicantParametersProvider;
        $this->applyRuleCategoriesRegistry = $applyRuleCategoriesRegistry;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getApplicants(array $applyRulesConfig = null)
    {
        try {
            $this->applyRulesConfig = $applyRulesConfig;
            $serializedConfig = md5(serialize($applyRulesConfig));
            $cachedApplicants = wp_cache_get(self::CACHE_KEY);
            if (false === $cachedApplicants || !isset($cachedApplicants[$serializedConfig])) {
                if (false === $cachedApplicants) {
                    $cachedApplicants = [];
                }
                $applicants = [];
                $applicantParameters = [];
                if ($applyRulesConfig === null || empty($applyRulesConfig)) {
                    return $applicants;
                }
                if (!is_array($applyRulesConfig['categories'])) {
                    $cachedApplicants[$serializedConfig] = $applicants;
                    wp_cache_set(self::CACHE_KEY, $cachedApplicants);
                } else {
                    foreach ($applyRulesConfig['categories'] as $categoryName => $configSet) {
                        $category = $this->applyRuleCategoriesRegistry->getCategory($categoryName);
                        if ($category && $category instanceof BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface) {
                            foreach (ApplyRuleConfigConverter::convert($configSet) as $key => $config) {
                                if (in_array($key, BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
                                    $applicants = $this->checkApplicantGroupConfig($config, $key);
                                } else {
                                    $applicants = $this->checkApplicantSingleConfig($config, $key);
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
                                $formattedParams = [];
                                foreach ($finalParams as $key => $values) {
                                    $formattedParams['$_' . $key] = [];
                                    foreach ($values as $value) {
                                        $formattedParams['$_' . $key][$value['key']] = $value['value'];
                                    }
                                }
                                $applicantsWithParams[sprintf('%s.%s', $k, $paramsString)] = new BuilderiusTemplateApplicant([
                                    BuilderiusTemplateApplicant::URL_FIELD => $applicant->getUrl(),
                                    BuilderiusTemplateApplicant::CATEGORY_FIELD => $applicant->getCategory(),
                                    BuilderiusTemplateApplicant::SORT_ORDER_FIELD => $applicant->getSortOrder(),
                                    BuilderiusTemplateApplicant::LABEL_FIELD => $applicant->getLabel() . ' - ' . json_encode($formattedParams, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE),
                                    BuilderiusTemplateApplicant::PARAMETERS_FIELD => $finalParams
                                ]);
                            }
                        }

                        $cachedApplicants[$serializedConfig] = $applicantsWithParams;
                        uasort($cachedApplicants[$serializedConfig], [AbstractTemplateApplicantsCacheUpdateHook::class, 'sortApplicants']);
                        wp_cache_set(self::CACHE_KEY, $cachedApplicants);
                    } else {
                        $cachedApplicants[$serializedConfig] = $applicants;
                        uasort($cachedApplicants[$serializedConfig], [AbstractTemplateApplicantsCacheUpdateHook::class, 'sortApplicants']);
                        wp_cache_set(self::CACHE_KEY, $cachedApplicants);
                    }
                }
            }

            return $cachedApplicants[$serializedConfig];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param array $subConfigs
     * @param string $conjunction
     * @return BuilderiusTemplateApplicantInterface[]
     */
    private function checkApplicantGroupConfig(array $subConfigs, $conjunction)
    {
        $applicantsSets = [];
        foreach ($subConfigs as $subConfig) {
            foreach ($subConfig as $key => $subSubConfig) {
                if (in_array($key, BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
                    $applicantsSets[] = $this->checkApplicantGroupConfig($subSubConfig, $key);
                } else {
                    $applicantsSets[] = $this->checkApplicantSingleConfig($subSubConfig, $key);
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
     * @param array $config
     * @param string $operator
     * @return BuilderiusTemplateApplicantInterface[]
     */
    private function checkApplicantSingleConfig(array $config, $operator)
    {
        $rule = $config[0]['var'];
        $argument = $config[1];

        $applicants = $this->applyRuleApplicantsProvider->getApplicants($rule, $argument, $operator);
        $event = new ApplicantSingleConfigEvent($applicants, $this->applyRulesConfig, $rule, $argument, $operator);
        $this->eventDispatcher->dispatch($event, 'builderius_template_single_config_applicants');

        return $event->getApplicants();
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