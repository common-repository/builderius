<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model;

class SimpleCondition extends \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition
{
    const FUNCTION_FIELD = 'function';
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        $function = $this->get(self::FUNCTION_FIELD);
        $arguments = $this->get(self::ARGUMENTS_FIELD, []);
        if (empty($arguments)) {
            return $function();
        }
        return \call_user_func_array($function, $arguments);
    }
}
