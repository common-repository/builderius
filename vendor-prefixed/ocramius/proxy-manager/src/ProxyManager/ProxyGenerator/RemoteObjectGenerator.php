<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator;

use Builderius\ProxyManager\Exception\InvalidProxiedClassException;
use Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils;
use Builderius\ProxyManager\Proxy\RemoteObjectInterface;
use Builderius\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicGet;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicIsset;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicSet;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicUnset;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\RemoteObjectMethod;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\StaticProxyConstructor;
use Builderius\ProxyManager\ProxyGenerator\RemoteObject\PropertyGenerator\AdapterProperty;
use Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ReflectionClass;
use ReflectionMethod;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Generator for proxies implementing {@see \Builderius\ProxyManager\Proxy\RemoteObjectInterface}
 *
 * {@inheritDoc}
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class RemoteObjectGenerator implements \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
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
        $interfaces = [\Builderius\ProxyManager\Proxy\RemoteObjectInterface::class];
        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }
        $classGenerator->setImplementedInterfaces($interfaces);
        $classGenerator->addPropertyFromGenerator($adapter = new \Builderius\ProxyManager\ProxyGenerator\RemoteObject\PropertyGenerator\AdapterProperty());
        \array_map(function (\Builderius\Zend\Code\Generator\MethodGenerator $generatedMethod) use($originalClass, $classGenerator) {
            \Builderius\ProxyManager\Generator\Util\ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
        }, \array_merge(\array_map(function (\ReflectionMethod $method) use($adapter, $originalClass) : RemoteObjectMethod {
            return \Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\RemoteObjectMethod::generateMethod(new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName()), $adapter, $originalClass);
        }, \Builderius\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter::getProxiedMethods($originalClass, ['__get', '__set', '__isset', '__unset'])), [new \Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\StaticProxyConstructor($originalClass, $adapter), new \Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicGet($originalClass, $adapter), new \Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicSet($originalClass, $adapter), new \Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicIsset($originalClass, $adapter), new \Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicUnset($originalClass, $adapter)]));
    }
}
