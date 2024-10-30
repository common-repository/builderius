<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BuilderiusSettingCheckerRequiredOptionsChainElement extends
 AbstractBuilderiusSettingComponentAwareCheckerChainElement
{
    /**
     * @inheritDoc
     */
    public function checkSetting(BuilderiusSettingInterface $setting)
    {
        $options = $setting->getOptions();
        $options['name'] = $setting->getName();
        $options['label'] = $setting->getLabel();

        $settingComponentName = $setting->getSettingComponent();
        $settingComponent = $this->settingsComponentsRegistry->getComponent($settingComponentName);
        $requiredOptions = [];
        foreach ($settingComponent->getAcceptableOptions() as $acceptableOption) {
            if ($acceptableOption->isRequired()) {
                $requiredOptions[] = $acceptableOption->getName();
            }
        }
        if (!empty($missingOptions = array_diff($requiredOptions, array_keys($options)))) {
            throw new \Exception(sprintf(
                'Missing required options "%s" for setting "%s"',
                implode('", "', $missingOptions), $settingComponentName)
            );
        }

        return true;
    }
}
