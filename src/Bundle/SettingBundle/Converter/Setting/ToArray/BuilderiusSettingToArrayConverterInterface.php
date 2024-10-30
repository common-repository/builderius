<?php

namespace Builderius\Bundle\SettingBundle\Converter\Setting\ToArray;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

interface BuilderiusSettingToArrayConverterInterface
{
    /**
     * @param BuilderiusSettingInterface $setting
     * @param string $formName
     * @param string $templateType
     * @param string $templateTechnology
     * @return array
     */
    public function convert(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    );
}
