<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface;
interface HooksRegistratorInterface
{
    /**
     * @param HookInterface[] $hooks
     */
    public function registerHooks(array $hooks);
}
