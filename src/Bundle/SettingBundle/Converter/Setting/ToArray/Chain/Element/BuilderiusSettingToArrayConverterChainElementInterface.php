<?php

namespace Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

interface BuilderiusSettingToArrayConverterChainElementInterface
{
    /**
     * @param BuilderiusSettingInterface $setting
     * @return bool
     */
    public function isApplicable(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    );

    /**
     * @param BuilderiusSettingInterface $setting
     * @return array
     */
    public function convertSetting(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    );
}
