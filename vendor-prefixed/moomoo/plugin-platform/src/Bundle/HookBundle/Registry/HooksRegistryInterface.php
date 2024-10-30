<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface;
interface HooksRegistryInterface
{
    /**
     * @return HookInterface[]
     */
    public function getHooks();
}
