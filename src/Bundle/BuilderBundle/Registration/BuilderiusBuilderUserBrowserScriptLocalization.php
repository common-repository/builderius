<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusBuilderUserBrowserScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'userBrowser';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

        if ($is_lynx) {
            return 'lynx';
        } elseif ($is_gecko) {
            return 'gecko';
        } elseif ($is_opera) {
            return 'opera';
        } elseif ($is_NS4) {
            return 'ns4';
        } elseif($is_safari) {
            return 'safari';
        } elseif($is_chrome) {
            return 'chrome';
        } elseif($is_IE) {
            if(preg_match('/MSIE ([0-9]+)([a-zA-Z0-9.]+)/', $_SERVER['HTTP_USER_AGENT'], $browser_version)) {
                return 'ie' . $browser_version[1];
            }
            return 'ie';
        } elseif ($is_iphone) {
            return 'iphone';
        } else {
            return 'unknown';
        }
    }
}
