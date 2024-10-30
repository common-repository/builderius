<?php

declare (strict_types=1);
namespace Builderius\ProxyManager;

use Builderius\ProxyManager\Autoloader\Autoloader;
use Builderius\ProxyManager\Autoloader\AutoloaderInterface;
use Builderius\ProxyManager\FileLocator\FileLocator;
use Builderius\ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use Builderius\ProxyManager\GeneratorStrategy\GeneratorStrategyInterface;
use Builderius\ProxyManager\Inflector\ClassNameInflector;
use Builderius\ProxyManager\Inflector\ClassNameInflectorInterface;
use Builderius\ProxyManager\Signature\ClassSignatureGenerator;
use Builderius\ProxyManager\Signature\ClassSignatureGeneratorInterface;
use Builderius\ProxyManager\Signature\SignatureChecker;
use Builderius\ProxyManager\Signature\SignatureCheckerInterface;
use Builderius\ProxyManager\Signature\SignatureGenerator;
use Builderius\ProxyManager\Signature\SignatureGeneratorInterface;
/**
 * Base configuration class for the proxy manager - serves as micro disposable DIC/facade
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class Configuration
{
    const DEFAULT_PROXY_NAMESPACE = 'ProxyManagerGeneratedProxy';
    /**
     * @var string|null
     */
    protected $proxiesTargetDir;
    /**
     * @var string
     */
    protected $proxiesNamespace = self::DEFAULT_PROXY_NAMESPACE;
    /**
     * @var GeneratorStrategyInterface|null
     */
    protected $generatorStrategy;
    /**
     * @var AutoloaderInterface|null
     */
    protected $proxyAutoloader;
    /**
     * @var ClassNameInflectorInterface|null
     */
    protected $classNameInflector;
    /**
     * @var SignatureGeneratorInterface|null
     */
    protected $signatureGenerator;
    /**
     * @var SignatureCheckerInterface|null
     */
    protected $signatureChecker;
    /**
     * @var ClassSignatureGeneratorInterface|null
     */
    protected $classSignatureGenerator;
    public function setProxyAutoloader(\Builderius\ProxyManager\Autoloader\AutoloaderInterface $proxyAutoloader) : void
    {
        $this->proxyAutoloader = $proxyAutoloader;
    }
    public function getProxyAutoloader() : \Builderius\ProxyManager\Autoloader\AutoloaderInterface
    {
        return $this->proxyAutoloader ?: ($this->proxyAutoloader = new \Builderius\ProxyManager\Autoloader\Autoloader(new \Builderius\ProxyManager\FileLocator\FileLocator($this->getProxiesTargetDir()), $this->getClassNameInflector()));
    }
    public function setProxiesNamespace(string $proxiesNamespace) : void
    {
        $this->proxiesNamespace = $proxiesNamespace;
    }
    public function getProxiesNamespace() : string
    {
        return $this->proxiesNamespace;
    }
    public function setProxiesTargetDir(string $proxiesTargetDir) : void
    {
        $this->proxiesTargetDir = $proxiesTargetDir;
    }
    public function getProxiesTargetDir() : string
    {
        return $this->proxiesTargetDir ?: ($this->proxiesTargetDir = \sys_get_temp_dir());
    }
    public function setGeneratorStrategy(\Builderius\ProxyManager\GeneratorStrategy\GeneratorStrategyInterface $generatorStrategy) : void
    {
        $this->generatorStrategy = $generatorStrategy;
    }
    public function getGeneratorStrategy() : \Builderius\ProxyManager\GeneratorStrategy\GeneratorStrategyInterface
    {
        return $this->generatorStrategy ?: ($this->generatorStrategy = new \Builderius\ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy());
    }
    public function setClassNameInflector(\Builderius\ProxyManager\Inflector\ClassNameInflectorInterface $classNameInflector) : void
    {
        $this->classNameInflector = $classNameInflector;
    }
    public function getClassNameInflector() : \Builderius\ProxyManager\Inflector\ClassNameInflectorInterface
    {
        return $this->classNameInflector ?: ($this->classNameInflector = new \Builderius\ProxyManager\Inflector\ClassNameInflector($this->getProxiesNamespace()));
    }
    public function setSignatureGenerator(\Builderius\ProxyManager\Signature\SignatureGeneratorInterface $signatureGenerator) : void
    {
        $this->signatureGenerator = $signatureGenerator;
    }
    public function getSignatureGenerator() : \Builderius\ProxyManager\Signature\SignatureGeneratorInterface
    {
        return $this->signatureGenerator ?: ($this->signatureGenerator = new \Builderius\ProxyManager\Signature\SignatureGenerator());
    }
    public function setSignatureChecker(\Builderius\ProxyManager\Signature\SignatureCheckerInterface $signatureChecker) : void
    {
        $this->signatureChecker = $signatureChecker;
    }
    public function getSignatureChecker() : \Builderius\ProxyManager\Signature\SignatureCheckerInterface
    {
        return $this->signatureChecker ?: ($this->signatureChecker = new \Builderius\ProxyManager\Signature\SignatureChecker($this->getSignatureGenerator()));
    }
    public function setClassSignatureGenerator(\Builderius\ProxyManager\Signature\ClassSignatureGeneratorInterface $classSignatureGenerator) : void
    {
        $this->classSignatureGenerator = $classSignatureGenerator;
    }
    public function getClassSignatureGenerator() : \Builderius\ProxyManager\Signature\ClassSignatureGeneratorInterface
    {
        return $this->classSignatureGenerator ?: new \Builderius\ProxyManager\Signature\ClassSignatureGenerator($this->getSignatureGenerator());
    }
}
