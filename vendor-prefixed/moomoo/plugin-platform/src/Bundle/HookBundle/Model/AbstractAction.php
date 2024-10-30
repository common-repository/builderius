<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Model;

abstract class AbstractAction extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractHook
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::ACTION_TYPE;
    }
}
