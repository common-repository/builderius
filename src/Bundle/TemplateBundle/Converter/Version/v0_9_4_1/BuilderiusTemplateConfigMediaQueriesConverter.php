<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_9_4_1;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\SettingBundle\Converter\Facade\ToArray\BuilderiusSettingFacadeToArrayConverter;
use Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\BuilderiusSettingToArrayConverterInterface;
use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionOrderedConverterInterface;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusTemplateConfigMediaQueriesConverter implements BuilderiusTemplateConfigVersionConverterInterface,
    BuilderiusTemplateConfigVersionOrderedConverterInterface
{
    const RESP_MODES_TO_MEDIA_QUERIES = [
        '1800_px' => '(max-width: 1800px)',
        '1200_px' => '(max-width: 1200px)',
        'desktop' => '(max-width: 1024px) and (max-height: 768px)',
        '900_px' => '(max-width: 900px)',
        'ipad' => '(max-width: 768px) and (max-height: 1024px)',
        '600_px' => '(max-width: 600px)',
        'iphone_xr' => '(max-width: 414px) and (max-height: 896px)',
        'pixel_3_2_nexus_5x_6p' => '(max-width: 412px) and (max-height: 730px)',
        'iphone_x_8_8_6_6s' => '(max-width: 375px) and (max-height: 670px)',
        'galaxy_s9_s8_s7' => '(max-width: 360px) and (max-height: 640px)',
    ];

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatner;

    /**
     * @var BuilderiusSettingToArrayConverterInterface
     */
    private $settingConverter;

    /**
     * @var array|null
     */
    private $templateSettingsWithDefaultValues;

    /**
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     * @param EventDispatcherInterface $eventDispatner
     * @param BuilderiusSettingToArrayConverterInterface $settingConverter
     */
    public function __construct(
        BuilderiusSettingsRegistryInterface $settingsRegistry,
        EventDispatcherInterface $eventDispatner,
        BuilderiusSettingToArrayConverterInterface $settingConverter
    ) {
        $this->settingsRegistry = $settingsRegistry;
        $this->eventDispatner = $eventDispatner;
        $this->settingConverter = $settingConverter;
    }


    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '0.9.4.1';
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
        $respModesNames = array_keys(self::RESP_MODES_TO_MEDIA_QUERIES);
        $templateType = $config['template']['type'];
        $templateTechnology = $config['template']['technology'];
        $filledTemplateSettings = [];
        if (isset($config['template']['settings']) && !empty($config['template']['settings'])) {
            foreach ($config['template']['settings'] as $idx => $templateSettingConfig) {
                $filledTemplateSettings[] = $templateSettingConfig['name'];
                $templateSetting = $this->settingsRegistry->getSetting(
                    $templateType,
                    $templateTechnology,
                    $templateSettingConfig['name']
                );
                if ($templateSetting && $templateSetting instanceof BuilderiusSettingCssAwareInterface) {
                    foreach ($templateSettingConfig['value'] as $respModeName => $value) {
                        if (in_array($respModeName, $respModesNames)) {
                            $config['template']['settings'][$idx]['value'][self::RESP_MODES_TO_MEDIA_QUERIES[$respModeName]] = $value;
                            unset($config['template']['settings'][$idx]['value'][$respModeName]);
                        }
                    }
                }
            }
        }
        $templateSettingsWithDefaultValues = $this->getTemplateSettingsWithDefaultValuesList($templateType, $templateTechnology);
        if (!empty($templateSettingsWithDefaultValues)) {
            foreach (array_keys($templateSettingsWithDefaultValues) as $settName) {
                if (!in_array($settName, $filledTemplateSettings)) {
                    $config['template']['settings'][] = [
                        'name' => $settName,
                        'value' => $templateSettingsWithDefaultValues[$settName]
                    ];
                }
            }
        }
        foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $moduleId => $moduleConfig) {
            if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                    $moduleSetting = $this->settingsRegistry->getSetting(
                        $templateType,
                        $templateTechnology,
                        $settingConfig['name']
                    );
                    if ($moduleSetting && $moduleSetting instanceof BuilderiusSettingCssAwareInterface) {
                        foreach ($settingConfig['value'] as $respModeName => $value) {
                            if (in_array($respModeName, $respModesNames)) {
                                $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][self::RESP_MODES_TO_MEDIA_QUERIES[$respModeName]] = $value;
                                unset($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD][$index]['value'][$respModeName]);
                            }
                        }
                    }
                }
            }
        }

        return $config;
    }

    /**
     * @param string $templateType
     * @param string $templateTechnology
     * @return array
     */
    private function getTemplateSettingsWithDefaultValuesList($templateType, $templateTechnology)
    {
        if (null === $this->templateSettingsWithDefaultValues) {
            $this->templateSettingsWithDefaultValues = [];
            $settingsList = [];
            foreach ($this->settingsRegistry->getSettings($templateType, $templateTechnology) as $setting) {
                foreach ($setting->getPaths() as $path) {
                    $event = new SettingContainingEvent($setting);
                    $this->eventDispatner->dispatch($event, sprintf('builderius_setting_convert_%s', $setting->getName()));
                    $settingsList[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                        $this->settingConverter->convert($event->getSetting());
                }
                foreach ($setting->getFacades() as $facade) {
                    foreach ($facade->getPaths() as $path) {
                        $settingsList[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                            BuilderiusSettingFacadeToArrayConverter::convert($facade);
                    }
                }
            }
            if (is_array($settingsList['template'])) {
                foreach ($settingsList['template'] as $settingsByCategories) {
                    foreach ($settingsByCategories as $settings) {
                        foreach ($settings as $settingConfig) {
                            if (isset($settingConfig['name']) && is_array($settingConfig['value']) && !empty($settingConfig['value'])) {
                                $this->templateSettingsWithDefaultValues[$settingConfig['name']] = $settingConfig['value'];
                            }
                        }
                    }
                }
            }
        }

        return $this->templateSettingsWithDefaultValues;
    }
}