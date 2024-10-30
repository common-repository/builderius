<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_10_0;

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
        return '0.10.0';
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
                    if (in_array(
                        $moduleConfig['name'],
                        [
                            'GenericBlock',
                            'Fieldset',
                            'Video',
                            'GenericInline',
                            'Blockquote',
                            'Label',
                            'Legend',
                            'Figcaption',
                            'Samp',
                            'ListItem',
                            'List',
                            'Anchor',
                            'Option',
                            'Caption',
                            'Output',
                            'Summary',
                            'RawHtml'
                        ])
                    ) {
                        $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId]['name'] = 'HtmlElement';
                        if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                            foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                                if (isset($settingConfig['name'])) {
                                    if (in_array(
                                        $settingConfig['name'],
                                        [
                                            'dataGenericInline',
                                            'dataBlockquote',
                                            'dataLabel',
                                            'dataLegend',
                                            'dataFigcaption',
                                            'dataSamp',
                                            'dataListItem',
                                            'dataList',
                                            'dataAnchor',
                                            'dataOption',
                                            'dataCaption',
                                            'dataOutput',
                                            'dataSummary',
                                            'dataRawHtml'
                                        ])
                                    ) {
                                        $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['name'] = 'dataHtmlElement';
                                    } elseif (
                                        in_array(
                                            $settingConfig['name'],
                                            [
                                                'htmlInlineTagContainer',
                                                'htmlTagContainer'
                                            ])
                                    ) {
                                        $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['name'] = 'htmlElementTag';
                                    } elseif ($settingConfig['name'] === 'href') {
                                        $foundHtmlAttribute = false;
                                        foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $i => $settConfig) {
                                            if ($settConfig['name'] === 'htmlAttribute') {
                                                if (isset($settConfig['value']) && isset($settConfig['value']['i1']) && is_array($settConfig['value']['i1'])) {
                                                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value']['i1'][] = [
                                                        'a1' => 'href',
                                                        'b1' => $settingConfig['value']['a1']
                                                    ];
                                                }
                                                $foundHtmlAttribute = true;
                                                break;
                                            }
                                        }
                                        if (false === $foundHtmlAttribute) {
                                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                                'name' => 'htmlAttribute',
                                                'value' => [
                                                    'i1' => [
                                                        [
                                                            'a1' => 'href',
                                                            'b1' => $settingConfig['value']['a1']
                                                        ]
                                                    ]
                                                ]
                                            ];
                                        }
                                        unset($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]);
                                    }
                                }
                            }
                            if ($moduleConfig['name'] === 'Blockquote') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'blockquote'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Label') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'label'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Legend') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'legend'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Figcaption') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'figcaption'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Samp') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'samp'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'ListItem') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'li'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'List') {
                                foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                                    if ($settingConfig['name'] === 'htmlTagList') {
                                        $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['name'] = 'htmlElementTag';
                                    }
                                }
                            } elseif ($moduleConfig['name'] === 'Anchor') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'a'
                                    ]
                                ];
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'isLinkWrapper',
                                    'value' => [
                                        'a1' => true
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Option') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'option'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Caption') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'caption'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Output') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'output'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Summary') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'summary'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Fieldset') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'fieldset'
                                    ]
                                ];
                            } elseif ($moduleConfig['name'] === 'Video') {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                    'name' => 'htmlElementTag',
                                    'value' => [
                                        'a1' => 'video'
                                    ]
                                ];
                            }
                        }
                    } elseif ($moduleConfig['name'] === 'Svg') {
                        $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId]['name'] = 'SvgCode';
                        if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                            foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                                if (isset($settingConfig['name']) && $settingConfig['name'] === 'dataSvg') {
                                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['name'] = 'dataSvgCode';
                                }
                            }
                        }
                    } elseif ($moduleConfig['name'] === 'Source') {
                        if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                            foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                                if (isset($settingConfig['name']) && $settingConfig['name'] === 'src') {
                                    $foundHtmlAttribute = false;
                                    foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $i => $settConfig) {
                                        if ($settConfig['name'] === 'htmlAttribute') {
                                            if (isset($settConfig['value']) && isset($settConfig['value']['i1']) && is_array($settConfig['value']['i1'])) {
                                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$i]['value']['i1'][] = [
                                                    'a1' => 'srcset',
                                                    'b1' => $settingConfig['value']['a1']
                                                ];
                                            }
                                            $foundHtmlAttribute = true;
                                            break;
                                        }
                                    }
                                    if (false === $foundHtmlAttribute) {
                                        $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][] = [
                                            'name' => 'htmlAttribute',
                                            'value' => [
                                                'i1' => [
                                                    [
                                                        'a1' => 'srcset',
                                                        'b1' => $settingConfig['value']['a1']
                                                    ]
                                                ]
                                            ]
                                        ];
                                    }
                                    unset($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]);
                                }
                            }
                        }
                    }
                    $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD] =
                        array_values($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD]);
                }
            }
        }

        return $config;
    }
}