<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface;
interface ConditionsRegistryInterface
{
    /**
     * @return ConditionInterface[]
     */
    public function getConditions();
    /**
     * @param string $name
     * @return ConditionInterface
     */
    public function getCondition($name);
    /**
     * @param string $name
     * @return bool
     */
    public function hasCondition($name);
}
