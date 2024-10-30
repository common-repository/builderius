<?php

namespace Builderius\Bundle\SettingBundle\Registry;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

interface BuilderiusSettingsRegistryInterface
{
    /**
     * @param string $templateType
     * @param string $technology
     * @param boolean $sort
     * @return BuilderiusSettingInterface[]
     */
    public function getSettings($templateType, $technology, $sort = true);

    /**
     * @param string $templateType
     * @param string $technology
     * @param string $name
     * @return BuilderiusSettingInterface
     */
    public function getSetting($templateType, $technology, $name);

    /**
     * @param string $templateType
     * @param string $technology
     * @param string $name
     * @return bool
     */
    public function hasSetting($templateType, $technology, $name);
}
