<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminBarNode;

class BuilderiusPreviewModeAdminNavBarNode extends AdminBarNode
{
    public function getTitle()
    {
        $user = apply_filters('builderius_get_current_user', wp_get_current_user());
        $devMode = get_user_meta($user->ID, 'builderius_dev_preview', false);
        $devMode = empty($devMode) ? true : (bool)$devMode[0];
        $status = $devMode ? 'dev' : 'prod';

        return __('Preview Mode', 'builderius') . '<a href="" id="builderius-preview-wrapper" class="' . $status . '"><span id="builderius-preview-mode-dev" class="builderius-preview-btn">DEV</span><span id="builderius-preview-mode-prod" class="builderius-preview-btn">PROD</span></a>';
    }

}