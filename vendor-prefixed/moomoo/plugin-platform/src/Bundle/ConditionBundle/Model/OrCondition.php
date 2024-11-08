<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model;

class OrCondition extends \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition
{
    /**
     * @var ConditionInterface[]
     */
    private $conditions = [];
    /**
     * @param ConditionInterface $condition
     * @return $this
     */
    public function addCondition(\Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface $condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        foreach ($this->conditions as $condition) {
            if ($condition->evaluate() === \true) {
                return \true;
            }
        }
        return \false;
    }
}
