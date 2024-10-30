<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator;

use Builderius\ProxyManager\Exception\InvalidProxiedClassException;
use Builderius\ProxyManager\Generator\MethodGenerator as ProxyManagerMethodGenerator;
use Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils;
use Builderius\ProxyManager\Proxy\GhostObjectInterface;
use Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use Builderius\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator\StaticProxyConstructor;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\CallInitializer;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\GetProxyInitializer;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\InitializeProxy;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\IsProxyInitialized;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicClone;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicGet;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicIsset;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicSet;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicSleep;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicUnset;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\SetProxyInitializer;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializationTracker;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializerProperty;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ReflectionClass;
use ReflectionMethod;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Generator for proxies implementing {@see \Builderius\ProxyManager\Proxy\GhostObjectInterface}
 *
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class LazyLoadingGhostGenerator implements \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidProxiedClassException
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function generate(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\ClassGenerator $classGenerator, array $proxyOptions = [])
    {
        \Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion::assertClassCanBeProxied($originalClass, \false);
        $filteredProperties = \Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass)->filter($proxyOptions['skippedProperties'] ?? []);
        $publicProperties = new \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap($filteredProperties);
        $privateProperties = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap($filteredProperties);
        $protectedProperties = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap($filteredProperties);
        $classGenerator->setExtendedClass($originalClass->getName());
        $classGenerator->setImplementedInterfaces([\Builderius\ProxyManager\Proxy\GhostObjectInterface::class]);
        $classGenerator->addPropertyFromGenerator($initializer = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializerProperty());
        $classGenerator->addPropertyFromGenerator($initializationTracker = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializationTracker());
        $classGenerator->addPropertyFromGenerator($publicProperties);
        $classGenerator->addPropertyFromGenerator($privateProperties);
        $classGenerator->addPropertyFromGenerator($protectedProperties);
        $init = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\CallInitializer($initializer, $initializationTracker, $filteredProperties);
        \array_map(function (\Builderius\Zend\Code\Generator\MethodGenerator $generatedMethod) use($originalClass, $classGenerator) {
            \Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
        }, \array_merge($this->getAbstractProxiedMethods($originalClass), [$init, new \Builderius\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator\StaticProxyConstructor($initializer, $filteredProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicGet($originalClass, $initializer, $init, $publicProperties, $protectedProperties, $privateProperties, $initializationTracker), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicSet($originalClass, $initializer, $init, $publicProperties, $protectedProperties, $privateProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicIsset($originalClass, $initializer, $init, $publicProperties, $protectedProperties, $privateProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicUnset($originalClass, $initializer, $init, $publicProperties, $protectedProperties, $privateProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicClone($originalClass, $initializer, $init), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicSleep($originalClass, $initializer, $init), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\SetProxyInitializer($initializer), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\GetProxyInitializer($initializer), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\InitializeProxy($initializer, $init), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\IsProxyInitialized($initializer)]));
    }
    /**
     * Retrieves all abstract methods to be proxied
     *
     * @param ReflectionClass $originalClass
     *
     * @return MethodGenerator[]
     */
    private function getAbstractProxiedMethods(\ReflectionClass $originalClass) : array
    {
        return \array_map(function (\ReflectionMethod $method) : ProxyManagerMethodGenerator {
            $generated = \Builderius\ProxyManager\Generator\MethodGenerator::fromReflectionWithoutBodyAndDocBlock(new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName()));
            $generated->setAbstract(\false);
            return $generated;
        }, \Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter::getAbstractProxiedMethods($originalClass));
    }
}
