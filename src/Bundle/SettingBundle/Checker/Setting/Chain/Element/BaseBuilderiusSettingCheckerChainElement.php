<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BaseBuilderiusSettingCheckerChainElement extends AbstractBuilderiusSettingCheckerChainElement
{
    /**
     * @inheritDoc
     */
    public function checkSetting(BuilderiusSettingInterface $setting)
    {
        if (!$setting->getName()) {
            throw new \Exception('There is no required property "name" for setting');
        }
        if (strpos($setting->getName(), ' ') !== false) {
            throw new \Exception("Setting name can't contain spaces");
        }
        if (sanitize_text_field($setting->getName()) !== $setting->getName()) {
            throw new \Exception("Setting name did not pass 'sanitize_text_field'");
        }
        if (!$setting->getLabel()) {
            throw new \Exception('There is no required property "label" for setting');
        }
        if (sanitize_text_field($setting->getLabel()) !== $setting->getLabel()) {
            throw new \Exception("Setting label did not pass 'sanitize_text_field'");
        }
        if (empty($setting->getPaths())) {
            throw new \Exception('There is no required property "paths" for setting');
        }
        if (!$setting->getSettingComponent()) {
            throw new \Exception('There is no required property "setting_component" for setting');
        }
        if ($setting instanceof BuilderiusSettingCssAwareInterface && !$setting->getValueExpressions()) {
            throw new \Exception('There is no required property "valueExpressions" for css setting');
        }

        return true;
    }
}
