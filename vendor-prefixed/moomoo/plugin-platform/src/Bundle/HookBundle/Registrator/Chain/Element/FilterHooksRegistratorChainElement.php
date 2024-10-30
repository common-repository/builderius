<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\FilterInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface;
class FilterHooksRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Registrator\Chain\Element\AbstractHooksRegistratorChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface $hook)
    {
        return $hook->getType() === \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface::FILTER_TYPE;
    }
    /**
     * @inheritDoc
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface $hook)
    {
        /** @var FilterInterface $hook */
        add_filter($hook->getTag(), function () use($hook) {
            if ($hook instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface) {
                $evaluated = \true;
                foreach ($hook->getLazyConditions() as $condition) {
                    if ($condition->evaluate() === \false) {
                        $evaluated = \false;
                        break;
                    }
                }
                if ($evaluated) {
                    return \call_user_func_array([$hook, 'getFunction'], \func_get_args());
                } else {
                    return $hook->returnOnFailedConditions(\func_get_args());
                }
            } else {
                return \call_user_func_array([$hook, 'getFunction'], \func_get_args());
            }
        }, $hook->getPriority(), $hook->getAcceptedArgs());
    }
}
