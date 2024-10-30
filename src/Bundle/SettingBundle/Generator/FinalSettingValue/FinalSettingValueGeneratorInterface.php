<?php

namespace Builderius\Bundle\SettingBundle\Generator\FinalSettingValue;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;

interface FinalSettingValueGeneratorInterface
{
    /**
     * @param BuilderiusSettingValueInterface $value
     * @param BuilderiusSettingValueExpressionInterface $valueExpression
     * @param array $settingSchema
     * @return mixed|null
     */
    public function generateFinalSettingValue(
        BuilderiusSettingValueInterface $value,
        BuilderiusSettingValueExpressionInterface $valueExpression,
        array $settingSchema
    );
}