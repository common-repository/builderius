<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class BuilderScriptTranslationsLoadHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        wp_set_script_translations( 'builderius-builder', 'builderius', WP_PLUGIN_DIR . '/builderius/languages-js' );
    }
}