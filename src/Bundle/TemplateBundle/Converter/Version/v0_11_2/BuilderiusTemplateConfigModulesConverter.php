<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_11_2;

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
        return '0.11.2';
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
                        if ($settConfig['name'] === 'htmlAttribute' && $settConfig['value']['i1'] && is_array($settConfig['value']['i1'])) {
                            foreach ($settConfig['value']['i1'] as $k => $item) {
                                if (in_array($item['a1'], ['data-section', 'data-when'])) {
                                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value']['i1'][$k]['a1'] = 'data-source';
                                } elseif (in_array($item['a1'], ['data-partial', 'data-if'])) {
                                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value']['i1'][$k]['a1'] = 'data-condition';
                                } elseif (in_array($item['a1'], ['data-partial-name', 'data-if-name'])) {
                                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value']['i1'][$k]['a1'] = 'data-recursive-name';
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