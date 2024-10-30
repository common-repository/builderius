<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingComponent\Chain\Element;

use Builderius\Bundle\SettingBundle\Checker\SettingComponent\BuilderiusSettingComponentCheckerInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingComponentInterface;

abstract class AbstractBuilderiusSettingComponentCheckerChainElement implements BuilderiusSettingComponentCheckerInterface
{
    /**
     * @var BuilderiusSettingComponentCheckerInterface|null
     */
    protected $successor;

    /**
     * @param BuilderiusSettingComponentCheckerInterface $checker
     */
    public function setSuccessor(BuilderiusSettingComponentCheckerInterface $checker)
    {
        $this->successor = $checker;
    }

    /**
     * @return BuilderiusSettingComponentCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function check(BuilderiusSettingComponentInterface $settingComponent)
    {
        $result = $this->checkSetting($settingComponent);
        
        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($settingComponent);
        } else {
            return $result;
        }
    }

    /**
     * @param BuilderiusSettingComponentInterface $settingComponent
     * @return bool
     */
    abstract protected function checkSetting(BuilderiusSettingComponentInterface $settingComponent);
}
