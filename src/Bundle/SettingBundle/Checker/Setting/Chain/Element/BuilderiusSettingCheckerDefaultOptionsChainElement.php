<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BuilderiusSettingCheckerDefaultOptionsChainElement extends
 AbstractBuilderiusSettingComponentAwareCheckerChainElement
{
    /**
     * @inheritDoc
     */
    public function checkSetting(BuilderiusSettingInterface $setting)
    {
        $options = $setting->getOptions();

        $settingComponentName = $setting->getSettingComponent();
        $settingComponent = $this->settingsComponentsRegistry->getComponent($settingComponentName);
        foreach ($settingComponent->getAcceptableOptions() as $acceptableOption) {
            if ($acceptableOption->getDefaultValue() !== null &&
                !isset($options[$acceptableOption->getName()])) {
                $options[$acceptableOption->getName()] = $acceptableOption->getDefaultValue();
            }
        }
        $setting->setOptions($options);

        return true;
    }
}
