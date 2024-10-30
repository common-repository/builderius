<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\ProxyManager\LazyProxy\Instantiator;

use Builderius\ProxyManager\Configuration;
use Builderius\ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use Builderius\ProxyManager\Proxy\LazyLoadingInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\InstantiatorInterface;
/**
 * Runtime lazy loading proxy generator.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RuntimeInstantiator implements \Builderius\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\InstantiatorInterface
{
    private $factory;
    public function __construct()
    {
        $config = new \Builderius\ProxyManager\Configuration();
        $config->setGeneratorStrategy(new \Builderius\ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy());
        $this->factory = new \Builderius\Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\LazyLoadingValueHolderFactory($config);
    }
    /**
     * {@inheritdoc}
     */
    public function instantiateProxy(\Builderius\Symfony\Component\DependencyInjection\ContainerInterface $container, \Builderius\Symfony\Component\DependencyInjection\Definition $definition, string $id, callable $realInstantiator)
    {
        return $this->factory->createProxy($this->factory->getGenerator()->getProxifiedClass($definition), function (&$wrappedInstance, \Builderius\ProxyManager\Proxy\LazyLoadingInterface $proxy) use($realInstantiator) {
            $wrappedInstance = $realInstantiator();
            $proxy->setProxyInitializer(null);
            return \true;
        });
    }
}
