<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Checker\Setting\BuilderiusSettingCheckerInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

abstract class AbstractBuilderiusSettingCheckerChainElement implements BuilderiusSettingCheckerInterface
{
    /**
     * @var BuilderiusSettingCheckerInterface|null
     */
    protected $successor;

    /**
     * @param BuilderiusSettingCheckerInterface $checker
     */
    public function setSuccessor(BuilderiusSettingCheckerInterface $checker)
    {
        $this->successor = $checker;
    }

    /**
     * @return BuilderiusSettingCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function check(BuilderiusSettingInterface $setting)
    {
        $result = $this->checkSetting($setting);
        
        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($setting);
        } else {
            return $result;
        }
    }

    /**
     * @param BuilderiusSettingInterface $setting
     * @return bool
     */
    abstract protected function checkSetting(BuilderiusSettingInterface $setting);
}
