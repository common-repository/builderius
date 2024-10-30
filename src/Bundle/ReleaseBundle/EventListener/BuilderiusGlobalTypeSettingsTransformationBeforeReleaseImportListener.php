<?php

namespace Builderius\Bundle\ReleaseBundle\EventListener;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener
{
    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var array
     */
    private $config;

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     */
    public function __construct(
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusSettingsRegistryInterface $settingsRegistry
    ) {
        $this->templateTypesProvider = $templateTypesProvider;
        $this->settingsRegistry = $settingsRegistry;
    }

    /**
     * @param ConfigContainingEvent $event
     */
    public function beforeImport(ConfigContainingEvent $event)
    {
        $this->config = $event->getConfig();
        if (isset($this->config['sub_modules'])) {
            $gssAllData = $this->getGssSubModule('all');
            if (null !== $gssAllData) {
                $this->config['sub_modules'][$gssAllData['index']]['name'] = 'Global Settings';
            }
            foreach ($this->templateTypesProvider->getTypes() as $type) {
                $typeName = $type->getName();
                $gssTypeData = $this->getGssSubModule($typeName);
                if (null !== $gssTypeData) {
                    $gssConfig = $gssTypeData['module']['content_config'];
                    $gssSettings = [];
                    if (isset($gssConfig['template']) && isset($gssConfig['template']['settings'])) {
                        $gssSettings = $gssConfig['template']['settings'];
                    }
                    if (!empty($gssSettings)) {
                        foreach ($this->config['sub_modules'] as $i => $subModule) {
                            if ($subModule['entity_type'] == 'template' && $subModule['technology'] == 'html' && $subModule['type'] === $typeName) {
                                $templateConfig = $subModule['content_config'];
                                if (!isset($templateConfig['template'])) {
                                    $templateConfig['template'] = [];
                                    $templateConfig['template']['settings'] = $gssSettings;
                                } else {
                                    $templateSettings = $templateConfig['template']['settings'];
                                    foreach ($gssSettings as $gssSetting) {
                                        $exists = false;
                                        $index = null;
                                        foreach ($templateSettings as $k => $templateSetting) {
                                            if ($gssSetting['name'] === $templateSetting['name']) {
                                                $exists = true;
                                                $index = $k;
                                                break;
                                            }
                                        }
                                        if (false === $exists) {
                                            $templateConfig['template']['settings'][] = $gssSetting;
                                        } else {
                                            $setting = $this->settingsRegistry->getSetting($typeName, 'html', $templateSettings[$index]['name']);
                                            if ($templateSettings[$index]['name'] === 'cssVars') {
                                                $templateConfig['template']['settings'][$index]['value'] =
                                                    BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener::processCssArraySetting(
                                                        'a2',
                                                        $templateSettings[$index]['value'],
                                                        $gssSetting['value']
                                                    );
                                            } elseif ($setting instanceof BuilderiusSettingCssAwareInterface) {
                                                $finalValue = $gssSetting;
                                                foreach ($templateSettings[$index]['value'] as $mediaQuery => $pseudoClassData) {
                                                    foreach ($pseudoClassData as $pseudoClass => $value) {
                                                        $finalValue[$mediaQuery][$pseudoClass] = $value;
                                                    }
                                                }
                                                $templateConfig['template']['settings'][$index]['value'] = $finalValue;
                                            } elseif (in_array($templateSettings[$index]['name'], ['dataVars', 'jsLibraries', 'cssLibraries'])) {
                                                $templateConfig['template']['settings'][$index]['value'] =
                                                    BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener::processNonCssArraySetting(
                                                        'b1',
                                                        $templateSettings[$index]['value'],
                                                        $gssSetting['value']
                                                    );
                                            } elseif (in_array($templateSettings[$index]['name'], ['htmlAttribute', 'customJs', 'customCss', 'stringTranslations'])) {
                                                $templateConfig['template']['settings'][$index]['value'] =
                                                    BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener::processNonCssArraySetting(
                                                        'a1',
                                                        $templateSettings[$index]['value'],
                                                        $gssSetting['value']
                                                    );
                                            }
                                        }
                                    }
                                }
                                $this->config['sub_modules'][$i]['content_config'] = $templateConfig;
                            }
                        }
                    }
                }
            }
            foreach ($this->config['sub_modules'] as $k => $subModule) {
                if ($subModule['entity_type'] == 'global_settings_set' && $subModule['technology'] == 'html' && $subModule['type'] !== 'all') {
                    unset($this->config['sub_modules'][$k]);
                }
                if ($subModule['entity_type'] == 'template') {
                    $this->config['sub_modules'][$k]['name'] = trim(explode('(tag', $this->config['sub_modules'][$k]['name'])[0]);
                }
            }
        }
        $event->setConfig($this->config);
    }

    /**
     * @param string $type
     * @return array|null
     */
    private function getGssSubModule($type)
    {
        if (isset($this->config['sub_modules'])) {
            foreach ($this->config['sub_modules'] as $i => $subModule) {
                if ($subModule['entity_type'] == 'global_settings_set' && $subModule['technology'] == 'html' && $subModule['type'] === $type) {
                    return ['index' => $i, 'module' => $subModule];
                }
            }
        }

        return null;
    }

    /**
     * @param array $templateValue
     * @param array $gssValue
     * @return array
     */
    public static function processCssArraySetting($nameParam, $templateValue, $gssValue) {
        $finalValue = $templateValue;
        foreach ($gssValue as $mediaQuery => $pseudoClassData) {
            foreach ($pseudoClassData as $pseudoClass => $value) {
                if (!isset($finalValue[$mediaQuery][$pseudoClass])) {
                    $finalValue[$mediaQuery][$pseudoClass] = $value;
                } else {
                    foreach ($value['i1'] as $gssItem) {
                        $exists = false;
                        foreach ($finalValue[$mediaQuery][$pseudoClass]['i1'] as $tItem) {
                            if ($gssItem[$nameParam] === $tItem[$nameParam]) {
                                $exists = true;
                                break;
                            }
                        }
                        if (false === $exists) {
                            $finalValue[$mediaQuery][$pseudoClass]['i1'][] = $gssItem;
                        }
                    }
                }
            }
        }

        return $finalValue;
    }

    /**
     * @param array $templateValue
     * @param array $gssValue
     * @return array
     */
    public static function processNonCssArraySetting($nameParam, $templateValue, $gssValue) {
        foreach ($gssValue['i1'] as $gssItem) {
            $exists = false;
            foreach ($templateValue['i1'] as $tItem) {
                if ($gssItem[$nameParam] === $tItem[$nameParam]) {
                    $exists = true;
                    break;
                }
            }
            if (false === $exists) {
                $templateValue['i1'][] = $gssItem;
            }
        }

        return $templateValue;
    }
}