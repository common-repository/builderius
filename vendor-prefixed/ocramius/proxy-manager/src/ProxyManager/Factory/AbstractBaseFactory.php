<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory;

use Builderius\ProxyManager\Configuration;
use Builderius\ProxyManager\Generator\ClassGenerator;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use Builderius\ProxyManager\Signature\Exception\InvalidSignatureException;
use Builderius\ProxyManager\Signature\Exception\MissingSignatureException;
use Builderius\ProxyManager\Version;
use ReflectionClass;
/**
 * Base factory common logic
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
abstract class AbstractBaseFactory
{
    /**
     * @var \Builderius\ProxyManager\Configuration
     */
    protected $configuration;
    /**
     * Cached checked class names
     *
     * @var string[]
     */
    private $checkedClasses = [];
    /**
     * @param \Builderius\ProxyManager\Configuration $configuration
     */
    public function __construct(\Builderius\ProxyManager\Configuration $configuration = null)
    {
        $this->configuration = $configuration ?: new \Builderius\ProxyManager\Configuration();
    }
    /**
     * Generate a proxy from a class name
     *
     * @param string  $className
     * @param mixed[] $proxyOptions
     *
     * @throws InvalidSignatureException
     * @throws MissingSignatureException
     * @throws \OutOfBoundsException
     */
    protected function generateProxy(string $className, array $proxyOptions = []) : string
    {
        if (\array_key_exists($className, $this->checkedClasses)) {
            return $this->checkedClasses[$className];
        }
        $proxyParameters = ['className' => $className, 'factory' => \get_class($this), 'proxyManagerVersion' => \Builderius\ProxyManager\Version::getVersion(), 'proxyOptions' => $proxyOptions];
        $proxyClassName = $this->configuration->getClassNameInflector()->getProxyClassName($className, $proxyParameters);
        if (!\class_exists($proxyClassName)) {
            $this->generateProxyClass($proxyClassName, $className, $proxyParameters, $proxyOptions);
        }
        $this->configuration->getSignatureChecker()->checkSignature(new \ReflectionClass($proxyClassName), $proxyParameters);
        return $this->checkedClasses[$className] = $proxyClassName;
    }
    protected abstract function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
    /**
     * Generates the provided `$proxyClassName` from the given `$className` and `$proxyParameters`
     *
     * @param string  $proxyClassName
     * @param string  $className
     * @param array   $proxyParameters
     * @param mixed[] $proxyOptions
     */
    private function generateProxyClass(string $proxyClassName, string $className, array $proxyParameters, array $proxyOptions = []) : void
    {
        $className = $this->configuration->getClassNameInflector()->getUserClassName($className);
        $phpClass = new \Builderius\ProxyManager\Generator\ClassGenerator($proxyClassName);
        $this->getGenerator()->generate(new \ReflectionClass($className), $phpClass, $proxyOptions);
        $phpClass = $this->configuration->getClassSignatureGenerator()->addSignature($phpClass, $proxyParameters);
        $this->configuration->getGeneratorStrategy()->generate($phpClass, $proxyOptions);
        $autoloader = $this->configuration->getProxyAutoloader();
        $autoloader($proxyClassName);
    }
}
