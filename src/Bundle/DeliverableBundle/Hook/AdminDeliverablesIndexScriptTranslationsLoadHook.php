<?php

namespace Builderius\Bundle\DeliverableBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class AdminDeliverablesIndexScriptTranslationsLoadHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        wp_set_script_translations( 'builderius-admin-deliverables-index', 'builderius', WP_PLUGIN_DIR . '/builderius/languages-js' );
    }
}