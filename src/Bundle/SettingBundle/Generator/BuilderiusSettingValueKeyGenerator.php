<?php

namespace Builderius\Bundle\SettingBundle\Generator;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;

class BuilderiusSettingValueKeyGenerator
{
    /**
     * @param BuilderiusSettingValueInterface $value
     * @return string
     */
    public static function generate(BuilderiusSettingValueInterface $value)
    {
        if ($value instanceof BuilderiusSettingCssValueInterface) {
            return sprintf('%s_%s', $value->getMediaQuery(), $value->getPseudoClass());
        }
        
        return BuilderiusSettingValue::VALUE_FIELD;
    }
}
