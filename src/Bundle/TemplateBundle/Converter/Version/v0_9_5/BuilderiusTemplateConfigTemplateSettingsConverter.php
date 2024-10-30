<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_9_5;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionOrderedConverterInterface;

class BuilderiusTemplateConfigTemplateSettingsConverter implements BuilderiusTemplateConfigVersionConverterInterface,
    BuilderiusTemplateConfigVersionOrderedConverterInterface
{
    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '0.9.5';
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * @inheritDoc
     */
    public function convert(array $config)
    {
        if (isset($config['template']) && isset($config['template'][BuilderiusModule::SETTINGS_FIELD])) {
            foreach ($config['template'][BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                if ($settingConfig['name'] === 'dataVars') {
                    if (isset($settingConfig['value']) && isset($settingConfig['value']['i1']) && is_array($settingConfig['value']['i1'])) {
                        foreach ($settingConfig['value']['i1'] as $k => $settingConfigItem) {
                            if (isset($settingConfigItem['a1']) && isset($settingConfigItem['c1']) && $settingConfigItem['a1'] === 'graphQLQuery') {
                                $c1 = str_replace('option_value(key:', 'option_value(name:', $settingConfigItem['c1']);
                                $config['template'][BuilderiusModule::SETTINGS_FIELD][$index]['value']['i1'][$k]['c1'] = $c1;
                            }
                        }
                    }
                } elseif($settingConfig['name'] === 'cssVars') {
                    if (isset($settingConfig['value']) && is_array($settingConfig['value'])) {
                        foreach ($settingConfig['value'] as $mediaQuery => $pseudoClassData) {
                            if (isset($pseudoClassData['i1'])) {
                                $config['template'][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery] = ['original' => ['i1' => $pseudoClassData['i1']]];
                            }
                        }
                    }
                }
            }
        }

        return $config;
    }
}