<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusAdminBarPreviewModeChangeScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const OBJECT_NAME = 'builderiusAdminBar';
    const PROPERTY_NAME = 'previewModeChange';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        /** @var \WP_User $currentUser */
        $currentUser = apply_filters('builderius_get_current_user', wp_get_current_user());
        $devMode = get_user_meta($currentUser->ID, 'builderius_dev_preview', false);
        $devMode = empty($devMode) ? true : (bool)$devMode[0];

        return [
            'mode' => $devMode ? 'prod' : 'dev',
            'link' => $this->getPreviewModeChangeUrl('action=builderius-preview_mode-change&preview_mode=' . ($devMode ? 'prod' : 'dev'))
        ];
    }

    private function getPreviewModeChangeUrl($str)
    {
        $pageURL = 'http';
        if(isset($_SERVER["HTTPS"]))
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        if(strpos($pageURL, '?') === false) {
            $pageURL .= '?' . $str;
        } else {
            $pageURL .= '&' . $str;
        }

        return $pageURL;
    }
}
