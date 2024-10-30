<?php

namespace Builderius\Bundle\TranslatePressBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class DisableLanguageSwitcherHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        return '';
    }
}