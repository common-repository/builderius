<?php

namespace Builderius\Bundle\SettingBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\SettingBundle\Converter\Facade\ToArray\BuilderiusSettingFacadeToArrayConverter;
use Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\BuilderiusSettingToArrayConverterInterface;
use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusSettingsListScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'settingsList';

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatner;

    /**
     * @var BuilderiusSettingToArrayConverterInterface
     */
    private $settingConverter;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $templatesProvider;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusSettingToArrayConverterInterface $settingConverter
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     * @param BuilderiusTemplateProviderInterface $templatesProvider
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusSettingToArrayConverterInterface $settingConverter,
        BuilderiusSettingsRegistryInterface $settingsRegistry,
        BuilderiusTemplateProviderInterface $templatesProvider
    ) {
        $this->eventDispatner = $eventDispatcher;
        $this->settingConverter = $settingConverter;
        $this->settingsRegistry = $settingsRegistry;
        $this->templatesProvider = $templatesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        $template = $this->templatesProvider->getTemplate();
        if ($template) {
            foreach ($this->settingsRegistry->getSettings($template->getType(), $template->getTechnology()) as $setting) {
                foreach ($setting->getPaths() as $path) {
                    if (
                        $template->getSubType() === 'hook' &&
                        $path->getForm()->getName() === 'template' &&
                        $path->getTab()->getName() === 'advanced'
                    ) {
                        continue;
                    }
                    $event = new SettingContainingEvent($setting);
                    $this->eventDispatner->dispatch($event, sprintf('builderius_setting_convert_%s', $setting->getName()));
                    $data[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                        $this->settingConverter->convert($event->getSetting());
                }
                foreach ($setting->getFacades() as $facade) {
                    foreach ($facade->getPaths() as $path) {
                        $data[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                            BuilderiusSettingFacadeToArrayConverter::convert($facade);
                    }
                }
            }
        }

        return $data;
    }
}
