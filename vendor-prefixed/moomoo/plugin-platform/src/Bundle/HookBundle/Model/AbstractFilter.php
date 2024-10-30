<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle\Model;

abstract class AbstractFilter extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractHook implements \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\FilterInterface
{
    const RETURN_ARGUMENT_ON_FAILED_CONDITIONS = 'return_argument_on_failed_conditions';
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::FILTER_TYPE;
    }
    /**
     * @inheritDoc
     */
    public function returnOnFailedConditions(array $args)
    {
        $number = $this->get(self::RETURN_ARGUMENT_ON_FAILED_CONDITIONS, 0);
        return $args[$number];
    }
}
