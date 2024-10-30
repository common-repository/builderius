<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BuilderiusSettingCheckerSettingComponentExistenceChainElement extends
 AbstractBuilderiusSettingComponentAwareCheckerChainElement
{
    /**
     * @inheritDoc
     */
    public function checkSetting(BuilderiusSettingInterface $setting)
    {
        $component = $setting->getSettingComponent();
        if (!$this->settingsComponentsRegistry->hasComponent($component)) {
            throw new \Exception(sprintf('There is no registered settingComponent with name "%s"', $component));
        }

        return true;
    }
}
