<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Checker\SettingValue\BuilderiusSettingValueCheckerInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;

abstract class AbstractBuilderiusSettingValueCheckerChainElement implements BuilderiusSettingValueCheckerInterface
{
    /**
     * @var BuilderiusSettingValueCheckerInterface|null
     */
    private $successor;

    /**
     * @param BuilderiusSettingValueCheckerInterface $checker
     */
    public function setSuccessor(BuilderiusSettingValueCheckerInterface $checker)
    {
        $this->successor = $checker;
    }

    /**
     * @return BuilderiusSettingValueCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function check(BuilderiusSettingValueInterface $settingValue, BuilderiusSettingInterface $setting)
    {
        $result = $this->checkValue($settingValue, $setting);

        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($settingValue, $setting);
        } else {
            return $result;
        }
    }

    /**
     * @param BuilderiusSettingValueInterface $settingValue
     * @param BuilderiusSettingInterface $setting
     * @return bool
     * @throws \Exception
     */
    abstract protected function checkValue(
        BuilderiusSettingValueInterface $settingValue,
        BuilderiusSettingInterface $setting
    );
}
