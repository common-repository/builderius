<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model;

class FunctionExistsCondition extends \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition
{
    const FUNCTION_FIELD = 'function';
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        return \function_exists($this->get(self::FUNCTION_FIELD));
    }
}
