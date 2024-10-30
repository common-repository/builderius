<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;

class BuilderiusSettingValueCheckerValueClassChainElement extends AbstractBuilderiusSettingValueCheckerChainElement
{
    /**
     * @inheritDoc
     */
    protected function checkValue(
        BuilderiusSettingValueInterface $settingValue,
        BuilderiusSettingInterface $setting
    ) {
        $settingName = $setting->getName();

        if ($setting instanceof BuilderiusSettingCssAwareInterface &&
            !$settingValue instanceof BuilderiusSettingCssValueInterface) {
            throw new \Exception(
                sprintf(
                    'SettingValue for Setting with css property which is equal "true" should be instance of "%s"
                    Problem found in setting "%s"',
                    BuilderiusSettingCssValueInterface::class,
                    $settingName
                )
            );
        }
        if (!$setting instanceof BuilderiusSettingCssAwareInterface &&
            $settingValue instanceof BuilderiusSettingCssValueInterface) {
            throw new \Exception(
                sprintf(
                    'SettingValue for Setting with css property which is equal "false" should not be instance of "%s"
                    Problem found in setting "%s"',
                    BuilderiusSettingCssValueInterface::class,
                    $settingName
                )
            );
        }

        return true;
    }
}
