<?php

namespace Builderius\Bundle\SettingBundle\Generator\GlobalSetting;

interface BuilderiusGlobalSettingValueGeneratorInterface
{
    /**
     * @return string
     */
    public function getSettingName();

    /**
     * @return string
     */
    public function getSettingGroup();

    /**
     * @param string $technology
     * @param array $valueConfig
     * @return mixed
     */
    public function generateSettingValue($technology, array $valueConfig);
}