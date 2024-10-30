<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Model;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
abstract class AbstractHook extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface, \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface
{
    const TAG_FIELD = 'tag';
    const PRIORITY_FIELD = 'priority';
    const ACCEPTED_ARGS_FIELD = 'accepted_args';
    const INIT_HOOK_NAME_FIELD = 'init_hook';
    const INIT_HOOK_PRIORITY_FIELD = 'init_hook_priority';
    use ConditionAwareTrait;
    /**
     * @inheritDoc
     */
    public function getInitHookName()
    {
        return $this->get(self::INIT_HOOK_NAME_FIELD, 'init');
    }
    /**
     * @inheritDoc
     */
    public function getInitHookPriority()
    {
        return $this->get(self::INIT_HOOK_PRIORITY_FIELD, 10);
    }
    /**
     * @return string
     */
    public function getTag()
    {
        return $this->get(self::TAG_FIELD);
    }
    /**
     * @return int
     */
    public function getAcceptedArgs()
    {
        return $this->get(self::ACCEPTED_ARGS_FIELD, 1);
    }
    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->get(self::PRIORITY_FIELD, 10);
    }
}
