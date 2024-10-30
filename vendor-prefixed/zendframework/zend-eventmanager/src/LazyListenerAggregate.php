<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-eventmanager for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-eventmanager/blob/master/LICENSE.md
 */
namespace Builderius\Zend\EventManager;

use Builderius\Interop\Container\ContainerInterface;
/**
 * Aggregate listener for attaching lazy listeners.
 *
 * Lazy listeners are listeners where creation is deferred until they are
 * triggered; this removes the most costly mechanism of pulling a listener
 * from a container unless the listener is actually invoked.
 *
 * Usage is:
 *
 * <code>
 * $events->attachAggregate(new LazyListenerAggregate(
 *     $lazyEventListenersOrDefinitions,
 *     $container
 * ));
 * </code>
 */
class LazyListenerAggregate implements \Builderius\Zend\EventManager\ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    /**
     * @var ContainerInterface Container from which to pull lazy listeners.
     */
    private $container;
    /**
     * @var array Additional environment/option variables to use when creating listener.
     */
    private $env;
    /**
     * Generated LazyEventListener instances.
     *
     * @var LazyEventListener[]
     */
    private $lazyListeners = [];
    /**
     * Constructor
     *
     * Accepts the composed $listeners, as well as the $container and $env in
     * order to create a listener aggregate that defers listener creation until
     * the listener is triggered.
     *
     * Listeners may be either LazyEventListener instances, or lazy event
     * listener definitions that can be provided to a LazyEventListener
     * constructor in order to create a new instance; in the latter case, the
     * $container and $env will be passed at instantiation as well.
     *
     * @var array $listeners LazyEventListener instances or array definitions
     *     to pass to the LazyEventListener constructor.
     * @var ContainerInterface $container
     * @var array $env
     * @throws Exception\InvalidArgumentException for invalid listener items.
     */
    public function __construct(array $listeners, \Builderius\Interop\Container\ContainerInterface $container, array $env = [])
    {
        $this->container = $container;
        $this->env = $env;
        // This would raise an exception for invalid structs
        foreach ($listeners as $listener) {
            if (\is_array($listener)) {
                $listener = new \Builderius\Zend\EventManager\LazyEventListener($listener, $container, $env);
            }
            if (!$listener instanceof \Builderius\Zend\EventManager\LazyEventListener) {
                throw new \Builderius\Zend\EventManager\Exception\InvalidArgumentException(\sprintf('All listeners must be LazyEventListener instances or definitions; received %s', \is_object($listener) ? \get_class($listener) : \gettype($listener)));
            }
            $this->lazyListeners[] = $listener;
        }
    }
    /**
     * Attach the aggregate to the event manager.
     *
     * Loops through all composed lazy listeners, and attaches them to the
     * event manager.
     *
     * @var EventManagerInterface $events
     * @var int $priority
     */
    public function attach(\Builderius\Zend\EventManager\EventManagerInterface $events, $priority = 1)
    {
        foreach ($this->lazyListeners as $lazyListener) {
            $this->listeners[] = $events->attach($lazyListener->getEvent(), $lazyListener, $lazyListener->getPriority($priority));
        }
    }
}
