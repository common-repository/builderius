<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class AdminHomeIndexScriptTranslationsLoadHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        wp_set_script_translations( 'builderius-admin-bar-admin', 'builderius', WP_PLUGIN_DIR . '/builderius/languages-js' );
    }
}