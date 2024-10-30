<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class WpAuthCheckHtmlHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        echo wp_auth_check_html();
    }
}
