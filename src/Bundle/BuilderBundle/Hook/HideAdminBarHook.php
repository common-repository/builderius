<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class HideAdminBarHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        return false;
    }
}
