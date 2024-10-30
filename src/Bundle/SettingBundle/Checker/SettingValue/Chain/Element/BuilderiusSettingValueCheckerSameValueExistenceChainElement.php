<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Generator\BuilderiusSettingValueKeyGenerator;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;

class BuilderiusSettingValueCheckerSameValueExistenceChainElement extends
AbstractBuilderiusSettingValueCheckerChainElement
{
    /**
     * @inheritDoc
     */
    protected function checkValue(
        BuilderiusSettingValueInterface $settingValue,
        BuilderiusSettingInterface $setting
    ) {
        $settingName = $setting->getName();
        $key = BuilderiusSettingValueKeyGenerator::generate($settingValue);
        $existingValues = $setting->getValues();
        if (isset($existingValues[$key]) && $existingValues[$key]->isDefault() === false) {
            throw new \Exception(
                sprintf(
                    'Same SettingValue for Setting "%s" already added',
                    $settingName
                )
            );
        }

        return true;
    }
}
