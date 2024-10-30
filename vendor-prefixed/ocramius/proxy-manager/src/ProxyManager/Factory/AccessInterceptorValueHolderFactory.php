<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory;

use Builderius\ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolderGenerator;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use Builderius\ProxyManager\Signature\Exception\InvalidSignatureException;
use Builderius\ProxyManager\Signature\Exception\MissingSignatureException;
/**
 * Factory responsible of producing proxy objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class AccessInterceptorValueHolderFactory extends \Builderius\ProxyManager\Factory\AbstractBaseFactory
{
    /**
     * @var \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolderGenerator|null
     */
    private $generator;
    /**
     * @param object     $instance           the object to be wrapped within the value holder
     * @param \Closure[] $prefixInterceptors an array (indexed by method name) of interceptor closures to be called
     *                                       before method logic is executed
     * @param \Closure[] $suffixInterceptors an array (indexed by method name) of interceptor closures to be called
     *                                       after method logic is executed
     *
     * @throws InvalidSignatureException
     * @throws MissingSignatureException
     * @throws \OutOfBoundsException
     */
    public function createProxy($instance, array $prefixInterceptors = [], array $suffixInterceptors = []) : \Builderius\ProxyManager\Proxy\AccessInterceptorValueHolderInterface
    {
        $proxyClassName = $this->generateProxy(\get_class($instance));
        return $proxyClassName::staticProxyConstructor($instance, $prefixInterceptors, $suffixInterceptors);
    }
    /**
     * {@inheritDoc}
     */
    protected function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
    {
        return $this->generator ?: ($this->generator = new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolderGenerator());
    }
}
