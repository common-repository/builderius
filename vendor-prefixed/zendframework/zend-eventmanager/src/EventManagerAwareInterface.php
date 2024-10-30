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
 * Interface to automate setter injection for an EventManager instance
 */
interface EventManagerAwareInterface extends \Builderius\Zend\EventManager\EventsCapableInterface
{
    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(\Builderius\Zend\EventManager\EventManagerInterface $eventManager);
}
