<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory;

use Builderius\ProxyManager\Proxy\NullObjectInterface;
use Builderius\ProxyManager\ProxyGenerator\NullObjectGenerator;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use Builderius\ProxyManager\Signature\Exception\InvalidSignatureException;
use Builderius\ProxyManager\Signature\Exception\MissingSignatureException;
/**
 * Factory responsible of producing proxy objects
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class NullObjectFactory extends \Builderius\ProxyManager\Factory\AbstractBaseFactory
{
    /**
     * @var \Builderius\ProxyManager\ProxyGenerator\NullObjectGenerator|null
     */
    private $generator;
    /**
     * @param object|string $instanceOrClassName the object to be wrapped or interface to transform to null object
     *
     * @throws InvalidSignatureException
     * @throws MissingSignatureException
     * @throws \OutOfBoundsException
     */
    public function createProxy($instanceOrClassName) : \Builderius\ProxyManager\Proxy\NullObjectInterface
    {
        $className = \is_object($instanceOrClassName) ? \get_class($instanceOrClassName) : $instanceOrClassName;
        $proxyClassName = $this->generateProxy($className);
        return $proxyClassName::staticProxyConstructor();
    }
    /**
     * {@inheritDoc}
     */
    protected function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
    {
        return $this->generator ?: ($this->generator = new \Builderius\ProxyManager\ProxyGenerator\NullObjectGenerator());
    }
}
