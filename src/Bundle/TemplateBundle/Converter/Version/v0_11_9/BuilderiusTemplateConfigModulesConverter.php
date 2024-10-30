<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_11_9;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionOrderedConverterInterface;

class BuilderiusTemplateConfigModulesConverter implements BuilderiusTemplateConfigVersionConverterInterface,
    BuilderiusTemplateConfigVersionOrderedConverterInterface
{
    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     */
    public function __construct(
        BuilderiusSettingsRegistryInterface $settingsRegistry
    ) {
        $this->settingsRegistry = $settingsRegistry;
    }


    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '0.11.9';
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
                    if ($moduleConfig['name'] === 'Source') {
                        $found = false;
                        if (isset($moduleConfig[BuilderiusModule::SETTINGS_FIELD])) {
                            foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $index => $settingConfig) {
                                $setting = $this->settingsRegistry->getSetting('template', 'html', $settingConfig['name']);
                                if ($setting instanceof BuilderiusSettingCssAwareInterface) {
                                    unset($moduleConfig[BuilderiusModule::SETTINGS_FIELD][$index]);
                                    $found = true;
                                }
                            }
                        }
                        if ($found === true) {
                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$moduleId][BuilderiusModule::SETTINGS_FIELD] = array_values($moduleConfig[BuilderiusModule::SETTINGS_FIELD]);
                        }
                    }
                }
            }
        }

        return $config;
    }
}