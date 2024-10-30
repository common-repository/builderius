<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
class ConditionAwareEventListener implements \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface
{
    use ConditionAwareTrait;
}
