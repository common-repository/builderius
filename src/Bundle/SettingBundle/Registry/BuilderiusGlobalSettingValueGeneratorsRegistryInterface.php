<?php

namespace Builderius\Bundle\SettingBundle\Registry;

use Builderius\Bundle\SettingBundle\Generator\GlobalSetting\BuilderiusGlobalSettingValueGeneratorInterface;

interface BuilderiusGlobalSettingValueGeneratorsRegistryInterface
{
    /**
     * @return BuilderiusGlobalSettingValueGeneratorInterface[]
     */
    public function getGenerators();

    /**
     * @param string $settingName
     * @return BuilderiusGlobalSettingValueGeneratorInterface|null
     */
    public function getGenerator($settingName);

    /**
     * @param string $settingName
     * @return boolean
     */
    public function hasGenerator($settingName);
}