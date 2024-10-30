<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\ApplyRule\BuilderiusTemplateApplyRuleInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Registry\BuilderiusTemplateApplyRulesRegistryInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateSubType\BuilderiusTemplateSubTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusTemplatePossibleApplyRulesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'possibleApplyRules';

    /**
     * @var BuilderiusTemplateApplyRulesRegistryInterface
     */
    private $templateApplyRulesRegistry;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusTemplateSubTypesProviderInterface
     */
    private $templateSubTypesProvider;

    /**
     * @param BuilderiusTemplateApplyRulesRegistryInterface $templateApplyRulesRegistry
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusTemplateSubTypesProviderInterface $templateSubTypesProvider
     */
    public function __construct(
        BuilderiusTemplateApplyRulesRegistryInterface $templateApplyRulesRegistry,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusTemplateSubTypesProviderInterface $templateSubTypesProvider
    ) {
        $this->templateApplyRulesRegistry = $templateApplyRulesRegistry;
        $this->templateTypesProvider = $templateTypesProvider;
        $this->templateSubTypesProvider = $templateSubTypesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $result = [];
        foreach ($this->templateApplyRulesRegistry->getRules() as $category => $rulesByNames) {
            $dataByTypes = [];
            /** @var BuilderiusTemplateApplyRuleInterface $rule */
            foreach ($rulesByNames as $rule) {
                if (!$rule->getParent()) {
                    $data = $this->convertRule($rule);
                    foreach ($this->getRuleTemplateTypes($rule) as $templateType) {
                        $subTypes = $this->templateSubTypesProvider->getSubTypes($templateType);
                        foreach ($subTypes as $subType) {
                            foreach ($data as $name => $config) {
                                $dataByTypes[$subType->getName()][$name] = $config;
                            }
                        }
                    }
                }
            }
            foreach ($dataByTypes as $type => $data) {
                ksort($data);
                $result[$type][$category] = $data;
            }
        }
        
        return $result;
    }

    /**
     * @param BuilderiusTemplateApplyRuleInterface $rule
     * @return array
     */
    private function getRuleTemplateTypes(BuilderiusTemplateApplyRuleInterface $rule)
    {
        if ($rule->isAppliedToAllTemplateTypes()) {
            $templateTypes = [];
            foreach ($this->templateTypesProvider->getTypes() as $templateType) {
                if ($templateType->getName() === 'template') {
                    $templateTypes[] = $templateType->getName();
                }
            }
            if (!empty($rule->getExcludedTemplateTypes())) {
                foreach ($rule->getExcludedTemplateTypes() as $excludedTemplateType) {
                    if (($key = array_search($excludedTemplateType, $templateTypes)) !== false) {
                        unset($templateTypes[$key]);
                    }
                }
            }

            return $templateTypes;
        } else {
            $ruleTypes = $rule->getTemplateTypes();
            foreach ($ruleTypes as $key => $ruleType) {
                $templateType = $this->templateTypesProvider->getType($ruleType);
                if (!$templateType || !$templateType->isStandalone()) {
                    unset($ruleTypes[$key]);
                }
            }

            return $ruleTypes;
        }
    }

    /**
     * @param BuilderiusTemplateApplyRuleInterface $rule
     * @return array
     */
    protected function convertRule(BuilderiusTemplateApplyRuleInterface $rule)
    {
        $data = [];
        if (!$rule->getChildren() && !$rule->getVariants()) {
            if ($rule->isSelectAllAllowed()) {
                $ruleName = $rule->getName();
                $data[$ruleName]['type'] = 'chain';
                $data[$ruleName]['label'] = $rule->getLabel();
                $data[$ruleName]['subfields'] = [];
                $data[$ruleName]['subfields']['all'] = [
                    'type' => 'select',
                    'preferWidgets' => ['select'],
                    'label' => $rule->getSelectAllLabel(),
                    'listValues' => [
                        [ 'value' => true, 'title' => 'true']
                    ],
                    'defaultValue' => true,
                    'valueSources' => ['value'],
                    'operators' => ['select_equals']
                ];
            } else if (!$rule->ignoreIfNoChildren() || !$rule->ignoreIfNoVariants()) {
                $data[$rule->getName()] = [
                    'type' => 'select',
                    'preferWidgets' => ['select'],
                    'label' => $rule->getLabel(),
                    'listValues' => [
                        ['value' => true, 'title' => 'true']
                    ],
                    'defaultValue' => true,
                    'valueSources' => ['value'],
                    'operators' => ['select_equals']
                ];
            }
        } else {
            $ruleName = $rule->getName();
            $data[$ruleName]['type'] = 'chain';
            $data[$ruleName]['label'] = $rule->getLabel();
            $data[$ruleName]['subfields'] = [];
            if ($rule->isSelectAllAllowed()) {
                $data[$ruleName]['subfields']['all'] = [
                    'type' => 'select',
                    'preferWidgets' => ['select'],
                    'label' => $rule->getSelectAllLabel(),
                    'listValues' => [
                        [ 'value' => true, 'title' => 'true']
                    ],
                    'defaultValue' => true,
                    'valueSources' => ['value'],
                    'operators' => ['select_equals']
                ];
            }
            foreach ($rule->getVariants() as $variant) {
                if ($variant->getPossibleArguments() !== null) {
                    $data[$ruleName]['subfields'][$variant->getName()] = [
                        'type' => $variant->getWidgetType() ? : 'select',
                        'preferWidgets' => [$variant->getWidgetType() ? : 'select'],
                        'label' => $variant->getLabel(),
                        'listValues' => $variant->getPossibleArguments(),
                        'valueSources' => ['value'],
                        'valuePlaceholder' => $variant->getPlaceholder() ? : '',
                        'operators' => $variant->getOperators() ? : ['select_equals', 'select_not_equals']
                    ];
                } else {
                    $data[$ruleName]['subfields'][$variant->getName()] = [
                        'type' => $variant->getWidgetType() ? : 'text',
                        'label' => $variant->getLabel(),
                        'valueSources' => ['value'],
                        'valuePlaceholder' => $variant->getPlaceholder() ? : '',
                        'operators' => $variant->getOperators() ? : ['equal', 'not_equal']
                    ];
                }
            }
            foreach ($rule->getChildren() as $childRule) {
                if (
                    $childRule->getChildren() ||
                    $childRule->getVariants() ||
                    $childRule->isSelectAllAllowed() ||
                    !$childRule->ignoreIfNoChildren() ||
                    !$childRule->ignoreIfNoVariants()
                ) {
                    $data[$ruleName]['subfields'][$childRule->getName()] =
                        $this->convertRule($childRule)[$childRule->getName()];
                }
            }
        }

        return $data;
    }
}
