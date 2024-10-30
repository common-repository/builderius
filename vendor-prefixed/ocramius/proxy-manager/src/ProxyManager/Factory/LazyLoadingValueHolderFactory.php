<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory;

use Builderius\ProxyManager\Proxy\VirtualProxyInterface;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
/**
 * Factory responsible of producing virtual proxy instances
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class LazyLoadingValueHolderFactory extends \Builderius\ProxyManager\Factory\AbstractBaseFactory
{
    /**
     * @var \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator|null
     */
    private $generator;
    public function createProxy(string $className, \Closure $initializer, array $proxyOptions = []) : \Builderius\ProxyManager\Proxy\VirtualProxyInterface
    {
        $proxyClassName = $this->generateProxy($className, $proxyOptions);
        return $proxyClassName::staticProxyConstructor($initializer);
    }
    /**
     * {@inheritDoc}
     */
    protected function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
    {
        return $this->generator ?: ($this->generator = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator());
    }
}
