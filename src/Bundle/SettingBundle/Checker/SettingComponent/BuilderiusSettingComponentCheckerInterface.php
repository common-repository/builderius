<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingComponent;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingComponentInterface;

interface BuilderiusSettingComponentCheckerInterface
{
    /**
     * @param BuilderiusSettingComponentInterface $settingComponent
     * @return boolean
     */
    public function check(BuilderiusSettingComponentInterface $settingComponent);
}
