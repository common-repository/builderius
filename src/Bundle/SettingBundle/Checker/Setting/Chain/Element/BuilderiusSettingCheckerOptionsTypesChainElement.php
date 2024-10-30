<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BuilderiusSettingCheckerOptionsTypesChainElement extends AbstractBuilderiusSettingComponentAwareCheckerChainElement
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
        $optionsTypes = [];
        foreach ($settingComponent->getAcceptableOptions() as $acceptableOption) {
            $optionsTypes[$acceptableOption->getName()] = $acceptableOption->getType();
        }
        foreach ($options as $name => $value) {
            if (!in_array($name, array_keys($optionsTypes))) {
                throw new \Exception(
                    sprintf(
                        'SettingComponent "%s" does not have option with name %s, allowed options are %s',
                        $settingComponentName,
                        $name,
                        implode(', ', array_keys($optionsTypes))
                    )
                );
            } elseif (gettype($value) !== $optionsTypes[$name]) {
                throw new \Exception(
                    sprintf(
                        'There is not correct option "%s" data type in "%s" setting_config,
                         data type should be %s, %s given',
                        $name,
                        $options['name'],
                        $optionsTypes[$name],
                        gettype($value)
                    )
                );
            }
        }

        return true;
    }
}
