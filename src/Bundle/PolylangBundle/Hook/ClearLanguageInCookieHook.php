<?php

namespace Builderius\Bundle\PolylangBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class ClearLanguageInCookieHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        unset($_COOKIE['pll_language']);
    }
}