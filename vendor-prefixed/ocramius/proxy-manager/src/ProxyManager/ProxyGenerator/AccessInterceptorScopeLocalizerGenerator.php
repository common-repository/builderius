<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator;

use Builderius\ProxyManager\Exception\InvalidProxiedClassException;
use Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils;
use Builderius\ProxyManager\Proxy\AccessInterceptorInterface;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodPrefixInterceptor;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodSuffixInterceptor;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\BindProxyProperties;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\InterceptedMethod;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicClone;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicGet;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicIsset;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSet;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSleep;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicUnset;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\StaticProxyConstructor;
use Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ReflectionClass;
use ReflectionMethod;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Generator for proxies implementing {@see \Builderius\ProxyManager\Proxy\ValueHolderInterface}
 * and localizing scope of the proxied object at instantiation
 *
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class AccessInterceptorScopeLocalizerGenerator implements \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
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
        \Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion::assertClassCanBeProxied($originalClass, \false);
        $classGenerator->setExtendedClass($originalClass->getName());
        $classGenerator->setImplementedInterfaces([\Builderius\ProxyManager\Proxy\AccessInterceptorInterface::class]);
        $classGenerator->addPropertyFromGenerator($prefixInterceptors = new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors());
        $classGenerator->addPropertyFromGenerator($suffixInterceptors = new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors());
        \array_map(function (\Builderius\Zend\Code\Generator\MethodGenerator $generatedMethod) use($originalClass, $classGenerator) {
            \Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
        }, \array_merge(\array_map($this->buildMethodInterceptor($prefixInterceptors, $suffixInterceptors), \Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter::getProxiedMethods($originalClass, ['__get', '__set', '__isset', '__unset', '__clone', '__sleep'])), [new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\StaticProxyConstructor($originalClass), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\BindProxyProperties($originalClass, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodPrefixInterceptor($prefixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodSuffixInterceptor($suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicGet($originalClass, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSet($originalClass, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicIsset($originalClass, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicUnset($originalClass, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSleep($originalClass, $prefixInterceptors, $suffixInterceptors), new \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicClone($originalClass, $prefixInterceptors, $suffixInterceptors)]));
    }
    private function buildMethodInterceptor(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors $prefixInterceptors, \Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors $suffixInterceptors) : callable
    {
        return function (\ReflectionMethod $method) use($prefixInterceptors, $suffixInterceptors) : InterceptedMethod {
            return \Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\InterceptedMethod::generateMethod(new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName()), $prefixInterceptors, $suffixInterceptors);
        };
    }
}
