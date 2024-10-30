<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;

class AddSsrToHtmlAttributesEventListener
{
    /**
     * @param ConfigContainingEvent $event
     */
    public function beforeRenderHtml(ConfigContainingEvent $event)
    {
        $fullConfig = $event->getConfig();
        $module = $fullConfig['module'];
        if ($module->hasSetting('interactiveMode')) {
            $htmlConfig = $fullConfig['htmlConfig'];
            $settings = $fullConfig['settings'];
            $ssr = true;
            foreach ($settings as $setting) {
                if ($setting['name'] === 'interactiveMode' && $setting['value']['a1'] === true) {
                    $ssr = false;
                    break;
                }
            }
            if (true === $ssr) {
                if (!isset($htmlConfig['htmlAttribute'])) {
                    $htmlConfig['htmlAttribute'] = [];
                }
                $htmlConfig['htmlAttribute'][] = [
                    'name' => 'data-ssr',
                    'value' => true
                ];
            }
            $fullConfig['htmlConfig'] = $htmlConfig;
            $event->setConfig($fullConfig);
        }
    }
}