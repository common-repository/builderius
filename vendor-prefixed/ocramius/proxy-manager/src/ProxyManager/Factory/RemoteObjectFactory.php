<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory;

use Builderius\ProxyManager\Configuration;
use Builderius\ProxyManager\Factory\RemoteObject\AdapterInterface;
use Builderius\ProxyManager\Proxy\RemoteObjectInterface;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use Builderius\ProxyManager\ProxyGenerator\RemoteObjectGenerator;
use Builderius\ProxyManager\Signature\Exception\InvalidSignatureException;
use Builderius\ProxyManager\Signature\Exception\MissingSignatureException;
/**
 * Factory responsible of producing remote proxy objects
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class RemoteObjectFactory extends \Builderius\ProxyManager\Factory\AbstractBaseFactory
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;
    /**
     * @var \Builderius\ProxyManager\ProxyGenerator\RemoteObjectGenerator|null
     */
    private $generator;
    /**
     * {@inheritDoc}
     *
     * @param AdapterInterface $adapter
     * @param Configuration    $configuration
     */
    public function __construct(\Builderius\ProxyManager\Factory\RemoteObject\AdapterInterface $adapter, \Builderius\ProxyManager\Configuration $configuration = null)
    {
        parent::__construct($configuration);
        $this->adapter = $adapter;
    }
    /**
     * @param string|object $instanceOrClassName
     *
     * @throws InvalidSignatureException
     * @throws MissingSignatureException
     * @throws \OutOfBoundsException
     */
    public function createProxy($instanceOrClassName) : \Builderius\ProxyManager\Proxy\RemoteObjectInterface
    {
        $proxyClassName = $this->generateProxy(\is_object($instanceOrClassName) ? \get_class($instanceOrClassName) : $instanceOrClassName);
        return $proxyClassName::staticProxyConstructor($this->adapter);
    }
    /**
     * {@inheritDoc}
     */
    protected function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
    {
        return $this->generator ?: ($this->generator = new \Builderius\ProxyManager\ProxyGenerator\RemoteObjectGenerator());
    }
}
