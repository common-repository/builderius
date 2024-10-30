<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsComponentsRegistryInterface;

abstract class AbstractBuilderiusSettingComponentAwareCheckerChainElement extends
 AbstractBuilderiusSettingCheckerChainElement
{
    /**
     * @var BuilderiusSettingsComponentsRegistryInterface
     */
    protected $settingsComponentsRegistry;

    /**
     * @param BuilderiusSettingsComponentsRegistryInterface $settingsComponentsRegistry
     */
    public function __construct(BuilderiusSettingsComponentsRegistryInterface $settingsComponentsRegistry)
    {
        $this->settingsComponentsRegistry = $settingsComponentsRegistry;
    }
}
