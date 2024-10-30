<?php

namespace Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\Chain\Element;

use Builderius\Bundle\SettingBundle\Checker\SettingValue\BuilderiusSettingValueCheckerInterface;
use Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\BuilderiusSettingToArrayConverterInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;

abstract class AbstractBuilderiusSettingToArrayConverter implements BuilderiusSettingToArrayConverterInterface, BuilderiusSettingToArrayConverterChainElementInterface
{
    /**
     * @var BuilderiusSettingValueCheckerInterface
     */
    protected $settingValueChecker;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    protected $settingsRegistry;

    /**
     * @param BuilderiusSettingValueCheckerInterface $settingValueChecker
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     */
    public function __construct(
        BuilderiusSettingValueCheckerInterface $settingValueChecker,
        BuilderiusSettingsRegistryInterface $settingsRegistry
    ) {
        $this->settingValueChecker = $settingValueChecker;
        $this->settingsRegistry = $settingsRegistry;
    }

    /**
     * @var BuilderiusSettingToArrayConverterChainElementInterface|null
     */
    protected $successor;

    /**
     * @param BuilderiusSettingToArrayConverterChainElementInterface $successor
     */
    public function setSuccessor(BuilderiusSettingToArrayConverterChainElementInterface $successor)
    {
        $this->successor = $successor;
    }

    /**
     * @return BuilderiusSettingToArrayConverterChainElementInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function convert(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    ) {
        if ($this->isApplicable($setting, $formName, $templateType, $templateTechnology)) {
            return $this->convertSetting($setting, $formName, $templateType, $templateTechnology);
        } elseif ($this->getSuccessor() && $this->getSuccessor()->isApplicable($setting, $formName, $templateType, $templateTechnology)) {
            return $this->getSuccessor()->convertSetting($setting, $formName, $templateType, $templateTechnology);
        }

        return null;
    }
}