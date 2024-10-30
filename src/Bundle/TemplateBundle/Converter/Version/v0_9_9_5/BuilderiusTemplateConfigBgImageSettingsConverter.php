<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_9_9_5;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;

class BuilderiusTemplateConfigBgImageSettingsConverter implements BuilderiusTemplateConfigVersionConverterInterface
{
    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '0.9.9.5';
    }

    /**
     * @inheritDoc
     */
    public function convert(array $config)
    {
        if (isset($config['template']) && isset($config['template'][BuilderiusModule::SETTINGS_FIELD])) {
            foreach ($config['template'][BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                if ($settingConfig['name'] === 'backgroundImage') {
                    foreach ($settingConfig['value'] as $mediaQuery => $pseudoClassData) {
                        foreach ($pseudoClassData as $pseudoClass => $value) {
                            if (isset($value['i1']) && is_array($value['i1'])) {
                                foreach ($value['i1'] as $k => $settingConfigItem) {
                                    if (isset($settingConfigItem['d1']) && is_string($settingConfigItem['d1'])) {
                                        $data = [];
                                        $lvl1Arr = explode('%,', $settingConfigItem['d1']);
                                        foreach ($lvl1Arr as $i => $lvl1Val) {
                                            $lvl2Arr = explode(') ', $lvl1Val);
                                            $data[] = [
                                                'id' => $i + 1,
                                                'pos' => (int)str_replace('%', '', trim($lvl2Arr[1])),
                                                'code' => trim($lvl2Arr[0]) . ')'
                                            ];
                                        }
                                        $config['template'][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['i1'][$k]['d1'] = $data;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (isset($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY]) && is_array($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
            foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $moduleId => $moduleConfig) {
                if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                    foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                        if ($settingConfig['name'] === 'backgroundImage') {
                            foreach ($settingConfig['value'] as $mediaQuery => $pseudoClassData) {
                                foreach ($pseudoClassData as $pseudoClass => $value) {
                                    if (isset($value['i1']) && is_array($value['i1'])) {
                                        foreach ($value['i1'] as $k => $settingConfigItem) {
                                            if (isset($settingConfigItem['d1']) && is_string($settingConfigItem['d1'])) {
                                                $data = [];
                                                $lvl1Arr = explode('%,', $settingConfigItem['d1']);
                                                foreach ($lvl1Arr as $i => $lvl1Val) {
                                                    $lvl2Arr = explode(') ', $lvl1Val);
                                                    $data[] = [
                                                        'id' => $i + 1,
                                                        'pos' => (int)str_replace('%', '', trim($lvl2Arr[1])),
                                                        'code' => trim($lvl2Arr[0]) . ')'
                                                    ];
                                                }
                                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['i1'][$k]['d1'] = $data;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $config;
    }
}