<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

abstract class AbstractApplicantDataAction extends AbstractAction
{
    /**
     * @return false|\WP_User|null
     */
    protected function getUser()
    {
        $user = wp_get_current_user();
        if ($user->ID === 0 && isset($_COOKIE[LOGGED_IN_COOKIE])) {
            $userId = wp_validate_auth_cookie( $_COOKIE[LOGGED_IN_COOKIE], 'logged_in' );
            if ($userId && $userId > 0) {
                $user = get_user_by('ID', $userId);
            }
        }

        return $user;
    }
}