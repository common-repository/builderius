<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class DetermineUserLocaleHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        return get_user_locale();
    }
}