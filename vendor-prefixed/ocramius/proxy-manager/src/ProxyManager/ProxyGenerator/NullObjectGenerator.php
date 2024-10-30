<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator;

use Builderius\ProxyManager\Exception\InvalidProxiedClassException;
use Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils;
use Builderius\ProxyManager\Proxy\NullObjectInterface;
use Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use Builderius\ProxyManager\ProxyGenerator\NullObject\MethodGenerator\NullObjectMethodInterceptor;
use Builderius\ProxyManager\ProxyGenerator\NullObject\MethodGenerator\StaticProxyConstructor;
use Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ReflectionClass;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Generator for proxies implementing {@see \Builderius\ProxyManager\Proxy\NullObjectInterface}
 *
 * {@inheritDoc}
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class NullObjectGenerator implements \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidProxiedClassException
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function generate(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\ClassGenerator $classGenerator)
    {
        \Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion::assertClassCanBeProxied($originalClass);
        $interfaces = [\Builderius\ProxyManager\Proxy\NullObjectInterface::class];
        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }
        $classGenerator->setImplementedInterfaces($interfaces);
        foreach (\Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter::getProxiedMethods($originalClass, []) as $method) {
            $classGenerator->addMethodFromGenerator(\Builderius\ProxyManager\ProxyGenerator\NullObject\MethodGenerator\NullObjectMethodInterceptor::generateMethod(new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName())));
        }
        \Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, new \Builderius\ProxyManager\ProxyGenerator\NullObject\MethodGenerator\StaticProxyConstructor($originalClass));
    }
}
