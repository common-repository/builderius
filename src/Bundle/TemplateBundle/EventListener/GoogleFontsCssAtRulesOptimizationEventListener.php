<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;

class GoogleFontsCssAtRulesOptimizationEventListener
{
    const GOOGLE_FONTS_IMPORT_URL_PART = '@import url("https://fonts.googleapis.com/css?family=';
    const GOOGLE_FONTS_IMPORT_URL_FULL = "https://fonts.googleapis.com/css?family=%s&display=swap";

    /**
     * @inheritDoc
     */
    public function onCssAtRulesSave(ConfigContainingEvent $event)
    {
        $atRules = $event->getConfig();
        $fonts = [];
        foreach ($atRules as $index => $atRule) {
            if (strpos($atRule, self::GOOGLE_FONTS_IMPORT_URL_PART) !== false) {
                $fonts[] =
                    str_replace(
                        '&display=swap");',
                        '',
                        str_replace(self::GOOGLE_FONTS_IMPORT_URL_PART, '', $atRule)
                    );
                unset($atRules[$index]);
            }
        }
        if (!empty($fonts)) {
            $atRules[] = sprintf(self::GOOGLE_FONTS_IMPORT_URL_FULL, implode('|', $fonts));
        }
        $event->setConfig($atRules);
    }
}