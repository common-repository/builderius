<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class LoadPluginTextDomainHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        load_plugin_textdomain( 'builderius', false, '/builderius/languages' );
    }
}