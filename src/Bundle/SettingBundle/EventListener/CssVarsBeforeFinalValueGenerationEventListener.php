<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionInterface;

class CssVarsBeforeFinalValueGenerationEventListener
{
    public function beforeFinalValueGeneration(SettingContainingEvent $event)
    {
        $setting = $event->getSetting();
        if ($setting->getName() === 'cssVars') {
            /** @var BuilderiusSettingValueExpressionInterface $expression */
            $expression = $setting->getValueExpressions()[0];
            $expression->setFormatExpression("join(items, ';')");
            $setting->setValueExpressions([$expression]);
            $event->setSetting($setting);
        }
    }
}