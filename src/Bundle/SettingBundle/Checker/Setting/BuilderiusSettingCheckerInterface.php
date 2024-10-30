<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

interface BuilderiusSettingCheckerInterface
{
    /**
     * @param BuilderiusSettingInterface $setting
     * @return boolean
     */
    public function check(BuilderiusSettingInterface $setting);
}
