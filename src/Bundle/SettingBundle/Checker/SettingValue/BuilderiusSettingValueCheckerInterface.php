<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingValue;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;

interface BuilderiusSettingValueCheckerInterface
{
    /**
     * @param BuilderiusSettingValueInterface $settingValue
     * @param BuilderiusSettingInterface $setting
     * @return boolean
     * @throws \Exception
     */
    public function check(BuilderiusSettingValueInterface $settingValue, BuilderiusSettingInterface $setting);
}
