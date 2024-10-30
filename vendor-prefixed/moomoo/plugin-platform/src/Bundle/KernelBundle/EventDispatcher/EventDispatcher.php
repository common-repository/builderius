<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\Psr\EventDispatcher\StoppableEventInterface;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;
class EventDispatcher extends \Builderius\Symfony\Component\EventDispatcher\EventDispatcher
{
    /**
     * @inheritDoc
     */
    protected function callListeners(iterable $listeners, string $eventName, object $event)
    {
        $stoppable = $event instanceof \Builderius\Psr\EventDispatcher\StoppableEventInterface;
        foreach ($listeners as $listener) {
            $listenerObject = $listener[0];
            if ($listenerObject instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $listenerObject->hasConditions()) {
                $evaluated = \true;
                foreach ($listenerObject->getConditions() as $condition) {
                    if ($condition->evaluate() === \false) {
                        $evaluated = \false;
                        break;
                    }
                }
                if (!$evaluated) {
                    continue;
                }
                if ($stoppable && $event->isPropagationStopped()) {
                    break;
                }
                $listener($event, $eventName, $this);
            } else {
                if ($stoppable && $event->isPropagationStopped()) {
                    break;
                }
                $listener($event, $eventName, $this);
            }
        }
    }
}
