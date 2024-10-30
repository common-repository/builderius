<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator;

use Builderius\ProxyManager\Exception\InvalidProxiedClassException;
use Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils;
use Builderius\ProxyManager\Proxy\VirtualProxyInterface;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\MagicWakeup;
use Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use Builderius\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator\StaticProxyConstructor;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\GetProxyInitializer;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\InitializeProxy;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\IsProxyInitialized;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\LazyLoadingMethodInterceptor;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicClone;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicGet;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicIsset;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicSet;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicSleep;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicUnset;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\SetProxyInitializer;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\InitializerProperty;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\GetWrappedValueHolderValue;
use ReflectionClass;
use ReflectionMethod;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Generator for proxies implementing {@see \Builderius\ProxyManager\Proxy\VirtualProxyInterface}
 *
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class LazyLoadingValueHolderGenerator implements \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidProxiedClassException
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function generate(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\ClassGenerator $classGenerator)
    {
        \Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion::assertClassCanBeProxied($originalClass);
        $interfaces = [\Builderius\ProxyManager\Proxy\VirtualProxyInterface::class];
        $publicProperties = new \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap(\Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass));
        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }
        $classGenerator->setImplementedInterfaces($interfaces);
        $classGenerator->addPropertyFromGenerator($valueHolder = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty());
        $classGenerator->addPropertyFromGenerator($initializer = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\InitializerProperty());
        $classGenerator->addPropertyFromGenerator($publicProperties);
        \array_map(function (\Builderius\Zend\Code\Generator\MethodGenerator $generatedMethod) use($originalClass, $classGenerator) {
            \Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
        }, \array_merge(\array_map($this->buildLazyLoadingMethodInterceptor($initializer, $valueHolder), \Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter::getProxiedMethods($originalClass)), [new \Builderius\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator\StaticProxyConstructor($initializer, \Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass)), \Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor::generateMethod($originalClass, $valueHolder), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicGet($originalClass, $initializer, $valueHolder, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicSet($originalClass, $initializer, $valueHolder, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicIsset($originalClass, $initializer, $valueHolder, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicUnset($originalClass, $initializer, $valueHolder, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicClone($originalClass, $initializer, $valueHolder), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicSleep($originalClass, $initializer, $valueHolder), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\MagicWakeup($originalClass), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\SetProxyInitializer($initializer), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\GetProxyInitializer($initializer), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\InitializeProxy($initializer, $valueHolder), new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\IsProxyInitialized($valueHolder), new \Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\GetWrappedValueHolderValue($valueHolder)]));
    }
    private function buildLazyLoadingMethodInterceptor(\Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\InitializerProperty $initializer, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty $valueHolder) : callable
    {
        return function (\ReflectionMethod $method) use($initializer, $valueHolder) : LazyLoadingMethodInterceptor {
            return \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\LazyLoadingMethodInterceptor::generateMethod(new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName()), $initializer, $valueHolder);
        };
    }
}
