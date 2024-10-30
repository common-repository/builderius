<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class DisableAutoUpdateHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $update = func_get_arg(0);
        $item = func_get_arg(1);
        $targetPlugin = 'builderius/builderius.php';

        if ($item->plugin === $targetPlugin) {
            return false;
        }

        return $update;
    }
}