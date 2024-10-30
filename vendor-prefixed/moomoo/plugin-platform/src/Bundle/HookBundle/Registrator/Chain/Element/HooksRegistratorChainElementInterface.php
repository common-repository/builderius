<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface;
interface HooksRegistratorChainElementInterface
{
    /**
     * @param HookInterface $hook
     * @return bool
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface $hook);
    /**
     * @param HookInterface $hook
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface $hook);
}
