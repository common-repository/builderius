<?php

namespace Builderius\Bundle\BuilderBundle\Condition;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class IsNotBuilderModeCondition extends AbstractCondition
{
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        if (array_key_exists('builderius', $_GET)) {
            return false;
        }

        return true;
    }
}