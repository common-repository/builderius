<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class PreviewModeChangingInitHook extends AbstractAction
{
    const APPLICATION = 'Builderius';

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        if (!isset($_REQUEST['action']) || $_REQUEST['action'] !== 'builderius-preview_mode-change') {
            return;
        }
        $current_user = apply_filters('builderius_get_current_user', wp_get_current_user());
        if (!$current_user || !user_can($current_user, 'builderius-development')) {
            return;
        }
        update_user_meta($current_user->ID, 'builderius_dev_preview', $_REQUEST['preview_mode'] === 'dev');
        wp_safe_redirect(add_query_arg([], $_SERVER['HTTP_REFERER']), 302, self::APPLICATION);
        exit;
    }
}