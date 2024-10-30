<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface;
class HooksRegistry implements \Builderius\MooMoo\Platform\Bundle\HookBundle\Registry\HooksRegistryInterface
{
    /**
     * @var HookInterface[]
     */
    private $hooks = [];
    /**
     * @param HookInterface $hook
     */
    public function addHook(\Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface $hook)
    {
        $this->hooks[] = $hook;
    }
    /**
     * @inheritDoc
     */
    public function getHooks()
    {
        return $this->hooks;
    }
}
