<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class AdminTemplatesIndexScriptTranslationsLoadHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        wp_set_script_translations( 'builderius-admin-templates-index', 'builderius', WP_PLUGIN_DIR . '/builderius/languages-js' );
    }
}