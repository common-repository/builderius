<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-eventmanager for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-eventmanager/blob/master/LICENSE.md
 */
namespace Builderius\Zend\EventManager;

/**
 * Abstract aggregate listener
 */
abstract class AbstractListenerAggregate implements \Builderius\Zend\EventManager\ListenerAggregateInterface
{
    /**
     * @var callable[]
     */
    protected $listeners = [];
    /**
     * {@inheritDoc}
     */
    public function detach(\Builderius\Zend\EventManager\EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            $events->detach($callback);
            unset($this->listeners[$index]);
        }
    }
}
