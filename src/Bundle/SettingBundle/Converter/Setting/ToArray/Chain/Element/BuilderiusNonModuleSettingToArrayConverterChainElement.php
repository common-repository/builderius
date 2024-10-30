<?php

namespace Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusModuleSettingInterface;
use Builderius\Bundle\SettingBundle\Model\AbstractBuilderiusSetting;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAtRule;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpression;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionInterface;

class BuilderiusNonModuleSettingToArrayConverterChainElement extends AbstractBuilderiusSettingToArrayConverter
{
    /**
     * @inheritDoc
     */
    public function isApplicable(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    ) {
        return !$setting instanceof BuilderiusModuleSettingInterface;
    }

    /**
     * @inheritDoc
     */
    public function convertSetting(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    ) {
        $paths = [];
        foreach ($setting->getPaths() as $path) {
            $paths[] = sprintf(
                '%s.%s.%s',
                $path->getForm()->getName(),
                $path->getTab()->getName(),
                $path->getCategory()->getName()
            );
        }

        $data = [
            AbstractBuilderiusSetting::NAME_FIELD => $setting->getName(),
            AbstractBuilderiusSetting::LABEL_FIELD => __($setting->getLabel()),
            AbstractBuilderiusSetting::SETTING_COMPONENT_FIELD => $setting->getSettingComponent(),
            AbstractBuilderiusSetting::DYNAMIC_DATA_FIELD => $setting->isDynamicDataAllowed(),
            AbstractBuilderiusSetting::SORT_ORDER_FIELD => $setting->getSortOrder(),
            BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
            BuilderiusSettingValue::VALUE_FIELD => $this->convertDefaultValues($setting, $formName, $templateType, $templateTechnology),
            AbstractBuilderiusSetting::OPTIONS_FIELD => $this->convertOptions($setting->getOptions()),
            AbstractBuilderiusSetting::VALUE_SCHEMA_FIELD => $setting->getValueSchema(),
            AbstractBuilderiusSetting::SHOW_IF_FIELD => $setting->getShowIf(),
            AbstractBuilderiusSetting::PATHS_FIELD => $paths
        ];
        if ($setting->isDynamicDataAllowed()) {
            $data[AbstractBuilderiusSetting::DYNAMIC_DATA_TYPES_FIELD] = $setting->getDynamicDataTypes();
        }
        foreach ($setting->getValueExpressions() as $valueExpression) {
            $data[AbstractBuilderiusSetting::VALUE_EXPRESSIONS_FIELD][] = $this->convertExpression($valueExpression);
        }
        if ($setting instanceof BuilderiusSettingCssAwareInterface) {
            $data[BuilderiusSettingCssAwareInterface::CSS_FIELD] = true;
            foreach ($setting->getAtRules() as $atRule) {
                $data[BuilderiusSettingCssAwareInterface::AT_RULES_FIELD][] = [
                    BuilderiusSettingCssAtRule::IDENTIFIER_FIELD =>
                        $atRule->getIdentifier(),
                    BuilderiusSettingCssAtRule::CONDITION_EXPRESSION_FIELD => $atRule->getConditionExpression(),
                    BuilderiusSettingCssAtRule::RULE_EXPRESSION_FIELD => $atRule->getRuleExpression(),
                ];
            }
        }

        return $data;
    }

    /**
     * @param BuilderiusSettingValueExpressionInterface $valueExpression
     * @return array
     */
    private function convertExpression(BuilderiusSettingValueExpressionInterface $valueExpression)
    {
        $dependsOnExpressions = [];
        foreach ($valueExpression->getDependsOnSettingValueExpressions() as $dependsOnExpression) {
            $dependsOnExpressions[] = $this->convertExpression($dependsOnExpression);
        }

        return [
            BuilderiusSettingValueExpression::NAME_FIELD =>
                $valueExpression->getName(),
            BuilderiusSettingValueExpression::CONDITION_EXPRESSION_FIELD => $valueExpression->getConditionExpression(),
            BuilderiusSettingValueExpression::FORMAT_EXPRESSION_FIELD => $valueExpression->getFormatExpression(),
            BuilderiusSettingValueExpression::CONTEXT_SOURCE_FIELD => $valueExpression->getContextSource(),
            BuilderiusSettingValueExpression::DEPENDS_ON_EXPRESSIONS => $dependsOnExpressions
        ];
    }

    /**
     * @param array $options
     * @return array
     */
    private function convertOptions(array $options)
    {
        if (isset($options['allow_empty'])) {
            if (isset($options['values']) && $options['allow_empty'] === true) {
                array_unshift($options['values'], '');
            }
            unset($options['allow_empty']);
        }

        return $options;
    }

    /**
     * @param BuilderiusSettingInterface $setting
     * @return array
     */
    private function convertDefaultValues(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    ) {
        $values = [];
        foreach ($setting->getDefaultValues(sprintf('%s_%s_%s', $formName, $templateType, $templateTechnology)) as $value) {
            if ($this->settingValueChecker->check($value, $setting)) {
                if ($value instanceof BuilderiusSettingCssValueInterface) {
                    $values[$value->getMediaQuery()][$value->getPseudoClass()] = $value->getValue();
                } else {
                    $values[] = $value->getValue();
                }
            }
        }
        if (count($values) === 1 && !$setting instanceof BuilderiusSettingCssAwareInterface) {
            $values = reset($values);
        }

        return $values;
    }
}
