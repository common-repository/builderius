<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminBarNode;

class BuilderiusAdminNavBarNode extends AdminBarNode
{
    public function getTitle()
    {
        $user = apply_filters('builderius_get_current_user', wp_get_current_user());
        $devMode = get_user_meta($user->ID, 'builderius_dev_preview', false);
        $devMode = empty($devMode) ? true : (bool)$devMode[0];
        $mode = $devMode ? 'dev' : 'prod';

        return '<img id="builderius-admin-bar-logo" src="' . plugins_url( 'builderius/assets/img/admin-menu-icon.svg') . '" alt="' . __('Builderius', 'builderius') . '" title="' . __('Builderius', 'builderius') . '"> <span class="ab-label">' . __('Builderius', 'builderius') . ' <i class="builderius-preview-mode builderius-preview-mode-' . $mode . '">&#9679;</i></span>';
    }

}