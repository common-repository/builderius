<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\EventDispatcher\DependencyInjection;

use Builderius\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Builderius\Symfony\Component\DependencyInjection\Reference;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;
/**
 * Compiler pass to register tagged services for an event dispatcher.
 */
class RegisterListenersPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    protected $dispatcherService;
    protected $listenerTag;
    protected $subscriberTag;
    protected $eventAliasesParameter;
    private $hotPathEvents = [];
    private $hotPathTagName;
    public function __construct(string $dispatcherService = 'event_dispatcher', string $listenerTag = 'kernel.event_listener', string $subscriberTag = 'kernel.event_subscriber', string $eventAliasesParameter = 'event_dispatcher.event_aliases')
    {
        $this->dispatcherService = $dispatcherService;
        $this->listenerTag = $listenerTag;
        $this->subscriberTag = $subscriberTag;
        $this->eventAliasesParameter = $eventAliasesParameter;
    }
    public function setHotPathEvents(array $hotPathEvents, $tagName = 'container.hot_path')
    {
        $this->hotPathEvents = \array_flip($hotPathEvents);
        $this->hotPathTagName = $tagName;
        return $this;
    }
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->dispatcherService) && !$container->hasAlias($this->dispatcherService)) {
            return;
        }
        $aliases = [];
        if ($container->hasParameter($this->eventAliasesParameter)) {
            $aliases = $container->getParameter($this->eventAliasesParameter);
        }
        $definition = $container->findDefinition($this->dispatcherService);
        foreach ($container->findTaggedServiceIds($this->listenerTag, \true) as $id => $events) {
            foreach ($events as $event) {
                $priority = isset($event['priority']) ? $event['priority'] : 0;
                if (!isset($event['event'])) {
                    if ($container->getDefinition($id)->hasTag($this->subscriberTag)) {
                        continue;
                    }
                    $event['method'] = $event['method'] ?? '__invoke';
                    $event['event'] = $this->getEventFromTypeDeclaration($container, $id, $event['method']);
                }
                $event['event'] = $aliases[$event['event']] ?? $event['event'];
                if (!isset($event['method'])) {
                    $event['method'] = 'on' . \preg_replace_callback(['/(?<=\\b)[a-z]/i', '/[^a-z0-9]/i'], function ($matches) {
                        return \strtoupper($matches[0]);
                    }, $event['event']);
                    $event['method'] = \preg_replace('/[^a-z0-9]/i', '', $event['method']);
                    if (null !== ($class = $container->getDefinition($id)->getClass()) && ($r = $container->getReflectionClass($class, \false)) && !$r->hasMethod($event['method']) && $r->hasMethod('__invoke')) {
                        $event['method'] = '__invoke';
                    }
                }
                $definition->addMethodCall('addListener', [$event['event'], [new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument(new \Builderius\Symfony\Component\DependencyInjection\Reference($id)), $event['method']], $priority]);
                if (isset($this->hotPathEvents[$event['event']])) {
                    $container->getDefinition($id)->addTag($this->hotPathTagName);
                }
            }
        }
        $extractingDispatcher = new \Builderius\Symfony\Component\EventDispatcher\DependencyInjection\ExtractingEventDispatcher();
        foreach ($container->findTaggedServiceIds($this->subscriberTag, \true) as $id => $attributes) {
            $def = $container->getDefinition($id);
            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $def->getClass();
            if (!($r = $container->getReflectionClass($class))) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            if (!$r->isSubclassOf(\Builderius\Symfony\Component\EventDispatcher\EventSubscriberInterface::class)) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Service "%s" must implement interface "%s".', $id, \Builderius\Symfony\Component\EventDispatcher\EventSubscriberInterface::class));
            }
            $class = $r->name;
            \Builderius\Symfony\Component\EventDispatcher\DependencyInjection\ExtractingEventDispatcher::$aliases = $aliases;
            \Builderius\Symfony\Component\EventDispatcher\DependencyInjection\ExtractingEventDispatcher::$subscriber = $class;
            $extractingDispatcher->addSubscriber($extractingDispatcher);
            foreach ($extractingDispatcher->listeners as $args) {
                $args[1] = [new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument(new \Builderius\Symfony\Component\DependencyInjection\Reference($id)), $args[1]];
                $definition->addMethodCall('addListener', $args);
                if (isset($this->hotPathEvents[$args[0]])) {
                    $container->getDefinition($id)->addTag($this->hotPathTagName);
                }
            }
            $extractingDispatcher->listeners = [];
            \Builderius\Symfony\Component\EventDispatcher\DependencyInjection\ExtractingEventDispatcher::$aliases = [];
        }
    }
    private function getEventFromTypeDeclaration(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container, string $id, string $method) : string
    {
        if (null === ($class = $container->getDefinition($id)->getClass()) || !($r = $container->getReflectionClass($class, \false)) || !$r->hasMethod($method) || 1 > ($m = $r->getMethod($method))->getNumberOfParameters() || !($type = $m->getParameters()[0]->getType()) instanceof \ReflectionNamedType || $type->isBuiltin() || \Builderius\Symfony\Contracts\EventDispatcher\Event::class === ($name = $type->getName())) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Service "%s" must define the "event" attribute on "%s" tags.', $id, $this->listenerTag));
        }
        return $name;
    }
}
/**
 * @internal
 */
class ExtractingEventDispatcher extends \Builderius\Symfony\Component\EventDispatcher\EventDispatcher implements \Builderius\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public $listeners = [];
    public static $aliases = [];
    public static $subscriber;
    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        $this->listeners[] = [$eventName, $listener[1], $priority];
    }
    public static function getSubscribedEvents() : array
    {
        $events = [];
        foreach ([self::$subscriber, 'getSubscribedEvents']() as $eventName => $params) {
            $events[self::$aliases[$eventName] ?? $eventName] = $params;
        }
        return $events;
    }
}
