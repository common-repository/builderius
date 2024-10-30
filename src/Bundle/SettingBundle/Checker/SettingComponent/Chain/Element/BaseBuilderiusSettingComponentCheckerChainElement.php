<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingComponent\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingComponentInterface;

class BaseBuilderiusSettingComponentCheckerChainElement extends AbstractBuilderiusSettingComponentCheckerChainElement
{
    /**
     * @inheritDoc
     */
    public function checkSetting(BuilderiusSettingComponentInterface $settingComponent)
    {
        if (!$settingComponent->getName()) {
            throw new \Exception('There is no required property "name" for settingComponent');
        }
        if (strpos($settingComponent->getName(), ' ') !== false) {
            throw new \Exception("SettingComponent name can't contain spaces");
        }
        if (sanitize_text_field($settingComponent->getName()) !== $settingComponent->getName()) {
            throw new \Exception("SettingComponent name did not pass 'sanitize_text_field'");
        }

        return true;
    }
}
