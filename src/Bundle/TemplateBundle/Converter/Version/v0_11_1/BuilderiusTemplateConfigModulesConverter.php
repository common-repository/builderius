<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_11_1;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionOrderedConverterInterface;

class BuilderiusTemplateConfigModulesConverter implements BuilderiusTemplateConfigVersionConverterInterface,
    BuilderiusTemplateConfigVersionOrderedConverterInterface
{
    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '0.11.1';
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
                if (isset($moduleConfig['name'])) {
                    foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $i => $settConfig) {
                        if ($settConfig['name'] === 'visibilityCondition' && $settConfig['value']['a1'] && is_string($settConfig['value']['a1'])) {
                            $expr = $settConfig['value']['a1'];
                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value'] = [
                                'i1' => [
                                    'rules' => [
                                        [
                                            'condition' => 'and',
                                            'type' => 'group',
                                            'rules' => [
                                                [
                                                    'type' => 'textarea',
                                                    'name' => 'expression',
                                                    'operator' => '==',
                                                    'value' => $expr
                                                ]
                                            ]
                                        ]
                                    ],
                                    'condition' => 'or',
                                    'type' => 'group'
                                ]
                            ];
                            break;
                        }
                    }
                    if (in_array(
                        $moduleConfig['name'],
                        [
                            'Collection',
                            'CollectionUL'
                        ])
                    ) {
                        foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $i => $settConfig) {
                            if ($settConfig['name'] === 'ssr') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['name'] = 'interactiveMode';
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value'] = [
                                    'a1' => !($settConfig['value']['a1'] == true)
                                ];
                                break;
                            }
                        }
                        $foundInteractiveMode = false;
                        foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD] as $settConfig) {
                            if ($settConfig['name'] === 'interactiveMode') {
                                $foundInteractiveMode = true;
                                break;
                            }
                        }
                        if (false === $foundInteractiveMode) {
                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                'name' => 'interactiveMode',
                                'value' => [
                                    'a1' => true
                                ]
                            ];
                        }
                    }
                }
            }
        }

        return $config;
    }
}