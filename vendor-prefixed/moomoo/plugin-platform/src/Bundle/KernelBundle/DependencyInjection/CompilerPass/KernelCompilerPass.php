<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Reference;
class KernelCompilerPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @var string
     */
    private $tag;
    /**
     * @var string
     */
    private $service;
    /**
     * @var string
     */
    private $method;
    /**
     * @param string $tag
     * @param string $service
     * @param string $method
     * @throws \Exception
     */
    public function __construct($tag, $service, $method)
    {
        if (!$tag || !$service || !$method) {
            throw new \Exception('missing parameter');
        }
        $this->tag = $tag;
        $this->service = $service;
        $this->method = $method;
    }
    /**
     * @inheritDoc
     */
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->service)) {
            return;
        }
        $taggedServices = $container->findTaggedServiceIds($this->tag);
        if (!$taggedServices) {
            return;
        }
        $elements = new \SplPriorityQueue();
        $definition = $container->getDefinition($this->service);
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = 0;
                if (\array_key_exists('priority', $tag)) {
                    $priority = $tag['priority'];
                }
                $elements->insert(new \Builderius\Symfony\Component\DependencyInjection\Reference($id), $priority);
            }
        }
        foreach ($elements as $element) {
            $definition->addMethodCall($this->method, [$element]);
        }
    }
}
