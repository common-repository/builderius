<?php


namespace Builderius\Bundle\SettingBundle\Provider;


use Builderius\Bundle\SettingBundle\Converter\Facade\ToArray\BuilderiusSettingFacadeToArrayConverter;
use Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\BuilderiusSettingToArrayConverterInterface;
use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefaultSettingsValuesProvider
{
    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusSettingToArrayConverterInterface
     */
    private $settingConverter;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusSettingToArrayConverterInterface $settingConverter
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        BuilderiusSettingsRegistryInterface $settingsRegistry,
        EventDispatcherInterface $eventDispatcher,
        BuilderiusSettingToArrayConverterInterface $settingConverter,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->settingsRegistry = $settingsRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->settingConverter = $settingConverter;
        $this->cache = $cache;
    }

    /**
     * @param string $formName
     * @param string $templateType
     * @param string $templateTechnology
     * @return array
     */
    public function getDefaultValues($formName, $templateType, $templateTechnology)
    {
        $defaultValues = $this->cache->get(sprintf('%s_%s_%s_settings_default_values', $formName, $templateType, $templateTechnology));
        if (false === $defaultValues) {
            $defaultValues = [];
            $settingsList = [];
            foreach ($this->settingsRegistry->getSettings($templateType, $templateTechnology) as $setting) {
                foreach ($setting->getPaths() as $path) {
                    $event = new SettingContainingEvent($setting);
                    $this->eventDispatcher->dispatch($event, sprintf('builderius_setting_convert_%s', $setting->getName()));
                    $settingsList[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                        $this->settingConverter->convert($event->getSetting(), $formName, $templateType, $templateTechnology);
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
                                $defaultValues[] = [
                                    'name' => $settingConfig['name'],
                                    'value' => $settingConfig['value']
                                ];
                            }
                        }
                    }
                }
            }
            $this->cache->set(sprintf('%s_%s_%s_settings_default_values', $formName, $templateType, $templateTechnology), $defaultValues);
            //$this->cache->set(sprintf('%s_all_%s_settings_default_values', $formName, $templateTechnology), $defaultValues);
        }

        return $defaultValues;
    }
}