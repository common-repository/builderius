<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory;

use Builderius\ProxyManager\Proxy\AccessInterceptorInterface;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizerGenerator;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use Builderius\ProxyManager\Signature\Exception\InvalidSignatureException;
use Builderius\ProxyManager\Signature\Exception\MissingSignatureException;
/**
 * Factory responsible of producing proxy objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class AccessInterceptorScopeLocalizerFactory extends \Builderius\ProxyManager\Factory\AbstractBaseFactory
{
    /**
     * @var \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizerGenerator|null
     */
    private $generator;
    /**
     * @param object     $instance           the object to be localized within the access interceptor
     * @param \Closure[] $prefixInterceptors an array (indexed by method name) of interceptor closures to be called
     *                                       before method logic is executed
     * @param \Closure[] $suffixInterceptors an array (indexed by method name) of interceptor closures to be called
     *                                       after method logic is executed
     *
     * @throws InvalidSignatureException
     * @throws MissingSignatureException
     * @throws \OutOfBoundsException
     */
    public function createProxy($instance, array $prefixInterceptors = [], array $suffixInterceptors = []) : \Builderius\ProxyManager\Proxy\AccessInterceptorInterface
    {
        $proxyClassName = $this->generateProxy(\get_class($instance));
        return $proxyClassName::staticProxyConstructor($instance, $prefixInterceptors, $suffixInterceptors);
    }
    /**
     * {@inheritDoc}
     */
    protected function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
    {
        return $this->generator ?: ($this->generator = new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizerGenerator());
    }
}
