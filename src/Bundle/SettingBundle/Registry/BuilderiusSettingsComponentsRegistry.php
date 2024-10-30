<?php

namespace Builderius\Bundle\SettingBundle\Registry;

use Builderius\Bundle\SettingBundle\Checker\SettingComponent\BuilderiusSettingComponentCheckerInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingComponentInterface;

class BuilderiusSettingsComponentsRegistry implements BuilderiusSettingsComponentsRegistryInterface
{
    /**
     * @var BuilderiusSettingComponentInterface[]
     */
    protected $settingComponents = [];
    
    /**
     * @var BuilderiusSettingComponentCheckerInterface
     */
    private $checker;

    /**
     * @param BuilderiusSettingComponentCheckerInterface $checker
     */
    public function __construct(BuilderiusSettingComponentCheckerInterface $checker)
    {
        $this->checker = $checker;
    }
    
    /**
     * @param BuilderiusSettingComponentInterface $settingComponent
     */
    public function addComponent(BuilderiusSettingComponentInterface $settingComponent)
    {
        if ($this->checker->check($settingComponent)) {
            $this->settingComponents[$settingComponent->getName()] = $settingComponent;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getComponents()
    {
        return $this->settingComponents;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent($name)
    {
        if ($this->hasComponent($name)) {
            return $this->settingComponents[$name];
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasComponent($name)
    {
        return isset($this->settingComponents[$name]);
    }
}
