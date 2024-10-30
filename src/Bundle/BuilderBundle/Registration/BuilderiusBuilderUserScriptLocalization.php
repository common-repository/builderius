<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusBuilderUserScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'user';

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
            'id' => (int)$currentUser->ID,
            'login' => $currentUser->user_login,
            'email' => $currentUser->user_email,
            'firstName' => $currentUser->first_name,
            'lastName' => $currentUser->last_name,
            'roles' => (array)$currentUser->roles,
            'builderiusPreviewMode' => $devMode ? 'dev' : 'prod'
        ];
    }
}
