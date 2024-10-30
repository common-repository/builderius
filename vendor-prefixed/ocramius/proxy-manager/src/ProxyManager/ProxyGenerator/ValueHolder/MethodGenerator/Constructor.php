<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
use Builderius\Zend\Code\Reflection\ParameterReflection;
/**
 * The `__construct` implementation for lazy loading proxies
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class Constructor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public static function generateMethod(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolder) : self
    {
        $originalConstructor = self::getConstructor($originalClass);
        /* @var $constructor self */
        $constructor = $originalConstructor ? self::fromReflectionWithoutBodyAndDocBlock($originalConstructor) : new self('__construct');
        $constructor->setBody('static $reflection;' . "\n\n" . 'if (! $this->' . $valueHolder->getName() . ') {' . "\n" . '    $reflection = $reflection ?: new \\ReflectionClass(' . \var_export($originalClass->getName(), \true) . ");\n" . '    $this->' . $valueHolder->getName() . ' = $reflection->newInstanceWithoutConstructor();' . "\n" . \Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator::generateSnippet(\Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass), 'this') . '}' . ($originalConstructor ? self::generateOriginalConstructorCall($originalConstructor, $valueHolder) : ''));
        return $constructor;
    }
    private static function generateOriginalConstructorCall(\Builderius\Zend\Code\Reflection\MethodReflection $originalConstructor, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolder) : string
    {
        return "\n\n" . '$this->' . $valueHolder->getName() . '->' . $originalConstructor->getName() . '(' . \implode(', ', \array_map(function (\Builderius\Zend\Code\Reflection\ParameterReflection $parameter) : string {
            return ($parameter->isVariadic() ? '...' : '') . '$' . $parameter->getName();
        }, $originalConstructor->getParameters())) . ');';
    }
    /**
     * @param ReflectionClass $class
     *
     * @return MethodReflection|null
     */
    private static function getConstructor(\ReflectionClass $class)
    {
        $constructors = \array_map(function (\ReflectionMethod $method) : MethodReflection {
            return new \Builderius\Zend\Code\Reflection\MethodReflection($method->getDeclaringClass()->getName(), $method->getName());
        }, \array_filter($class->getMethods(), function (\ReflectionMethod $method) : bool {
            return $method->isConstructor();
        }));
        return \reset($constructors) ?: null;
    }
}
