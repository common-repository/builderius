<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_9_5;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionOrderedConverterInterface;

class BuilderiusTemplateConfigModulesSettingsConverter implements BuilderiusTemplateConfigVersionConverterInterface,
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
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function convert(array $config)
    {
        if (isset($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY]) && is_array($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
            foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $moduleId => $moduleConfig) {
                if (in_array($moduleConfig['name'], ['Row', 'Column'])) {
                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId]['name'] = 'GenericBlock';
                }
                if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                    foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                        if ($settingConfig['name'] === 'font') {
                            foreach ($settingConfig['value'] as $mediaQuery => $pseudoClassData) {
                                foreach ($pseudoClassData as $pseudoClass => $value) {
                                    if (isset($value['a1']) && $value['a1'] === 'google') {
                                        if ($value['c1'] === 'Muli') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Mulish';
                                        } elseif ($value['c1'] === 'Lacquer') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['b1'] = 'display';
                                        } elseif ($value['c1'] === 'Laila') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['b1'] = 'sans-serif';
                                        } elseif ($value['c1'] === 'Baloo') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo 2';
                                        } elseif ($value['c1'] === 'Baloo Bhai') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Bhai 2';
                                        } elseif ($value['c1'] === 'Baloo Bhaijaan') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Bhai 2';
                                        } elseif ($value['c1'] === 'Baloo Bhaina') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Bhaina 2';
                                        } elseif ($value['c1'] === 'Baloo Chettan') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Chettan 2';
                                        } elseif ($value['c1'] === 'Baloo Da') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Da 2';
                                        } elseif ($value['c1'] === 'Baloo Paaji') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Paaji 2';
                                        } elseif ($value['c1'] === 'Baloo Tamma') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Tamma 2';
                                        } elseif ($value['c1'] === 'Baloo Tammudu') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Tammudu 2';
                                        } elseif ($value['c1'] === 'Baloo Thambi') {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$mediaQuery][$pseudoClass]['c1'] = 'Baloo Thambi 2';
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