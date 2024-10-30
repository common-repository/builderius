<?php

namespace Builderius\Bundle\SettingBundle\Factory;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingComponentOption;

class BuilderiusSettingComponentOptionFactory
{
    /**
     * @param array $arguments
     * @return BuilderiusSettingComponentOption
     * @throws \Exception
     */
    public static function create(array $arguments)
    {
        if (!isset($arguments[BuilderiusSettingComponentOption::NAME_FIELD])) {
            throw new \Exception('There is no required property "name" for settingOption');
        }
        if (!isset($arguments[BuilderiusSettingComponentOption::TYPE_FIELD])) {
            throw new \Exception('There is no required property "type" for settingOption');
        }
        if (!isset($arguments[BuilderiusSettingComponentOption::REQUIRED_FIELD])) {
            $arguments[BuilderiusSettingComponentOption::REQUIRED_FIELD] = false;
        }
        if (!isset($arguments[BuilderiusSettingComponentOption::DEFAULT_FIELD])) {
            $arguments[BuilderiusSettingComponentOption::DEFAULT_FIELD] = null;
        }

        return new BuilderiusSettingComponentOption($arguments);
    }
}
