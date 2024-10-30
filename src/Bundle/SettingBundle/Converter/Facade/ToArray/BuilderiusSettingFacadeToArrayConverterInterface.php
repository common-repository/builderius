<?php

namespace Builderius\Bundle\SettingBundle\Converter\Facade\ToArray;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingFacadeInterface;

interface BuilderiusSettingFacadeToArrayConverterInterface
{
    /**
     * @param BuilderiusSettingFacadeInterface $facade
     * @return array
     */
    public static function convert(BuilderiusSettingFacadeInterface $facade);
}
