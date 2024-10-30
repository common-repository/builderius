<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator;

use Builderius\ProxyManager\Exception\InvalidProxiedClassException;
use Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils;
use Builderius\ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\MagicWakeup;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodPrefixInterceptor;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodSuffixInterceptor;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\InterceptedMethod;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicClone;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicGet;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicIsset;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicSet;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicUnset;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\StaticProxyConstructor;
use Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\GetWrappedValueHolderValue;
use Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\MagicSleep;
use ReflectionClass;
use ReflectionMethod;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Generator for proxies implementing {@see \Builderius\ProxyManager\Proxy\ValueHolderInterface}
 * and {@see \Builderius\ProxyManager\Proxy\AccessInterceptorInterface}
 *
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class AccessInterceptorValueHolderGenerator implements \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     * @throws InvalidProxiedClassException
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function generate(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\ClassGenerator $classGenerator)
    {
        \Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion::assertClassCanBeProxied($originalClass);
        $publicProperties = new \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap(\Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass));
        $interfaces = [\Builderius\ProxyManager\Proxy\AccessInterceptorValueHolderInterface::class];
        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }
        $classGenerator->setImplementedInterfaces($interfaces);
        $classGenerator->addPropertyFromGenerator($valueHolder = new \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty());
        $classGenerator->addPropertyFromGenerator($prefixInterceptors = new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors());
        $classGenerator->addPropertyFromGenerator($suffixInterceptors = new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors());
        $classGenerator->addPropertyFromGenerator($publicProperties);
        \array_map(function (\Builderius\Zend\Code\Generator\MethodGenerator $generatedMethod) use($originalClass, $classGenerator) {
            \Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
        }, \array_merge(\array_map($this->buildMethodInterceptor($prefixInterceptors, $suffixInterceptors, $valueHolder), \Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter::getProxiedMethods($originalClass)), [\Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor::generateMethod($originalClass, $valueHolder), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\StaticProxyConstructor($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\GetWrappedValueHolderValue($valueHolder), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodPrefixInterceptor($prefixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodSuffixInterceptor($suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicGet($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicSet($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicIsset($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicUnset($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors, $publicProperties), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicClone($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\MagicSleep($originalClass, $valueHolder), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\MagicWakeup($originalClass)]));
    }
    private function buildMethodInterceptor(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors $prefixes, \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors $suffixes, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty $valueHolder) : callable
    {
        return function (\ReflectionMethod $method) use($prefixes, $suffixes, $valueHolder) : InterceptedMethod {
            return \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\InterceptedMethod::generateMethod(new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName()), $valueHolder, $prefixes, $suffixes);
        };
    }
}
