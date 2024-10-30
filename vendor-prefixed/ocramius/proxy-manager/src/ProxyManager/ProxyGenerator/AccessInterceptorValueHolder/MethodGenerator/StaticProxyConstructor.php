<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * The `staticProxyConstructor` implementation for access interceptor value holders
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class StaticProxyConstructor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass   $originalClass
     * @param PropertyGenerator $valueHolder
     * @param PropertyGenerator $prefixInterceptors
     * @param PropertyGenerator $suffixInterceptors
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolder, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors)
    {
        parent::__construct('staticProxyConstructor', [], static::FLAG_PUBLIC | static::FLAG_STATIC);
        $prefix = new \Builderius\Zend\Code\Generator\ParameterGenerator('prefixInterceptors');
        $suffix = new \Builderius\Zend\Code\Generator\ParameterGenerator('suffixInterceptors');
        $prefix->setDefaultValue([]);
        $suffix->setDefaultValue([]);
        $prefix->setType('array');
        $suffix->setType('array');
        $this->setParameter(new \Builderius\Zend\Code\Generator\ParameterGenerator('wrappedObject'));
        $this->setParameter($prefix);
        $this->setParameter($suffix);
        $this->setReturnType($originalClass->getName());
        $this->setDocBlock("Constructor to setup interceptors\n\n" . "@param \\" . $originalClass->getName() . " \$wrappedObject\n" . "@param \\Closure[] \$prefixInterceptors method interceptors to be used before method logic\n" . "@param \\Closure[] \$suffixInterceptors method interceptors to be used before method logic\n\n" . '@return self');
        $this->setBody('static $reflection;' . "\n\n" . '$reflection = $reflection ?? $reflection = new \\ReflectionClass(__CLASS__);' . "\n" . '$instance = $reflection->newInstanceWithoutConstructor();' . "\n\n" . \Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator::generateSnippet(\Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass), 'instance') . '$instance->' . $valueHolder->getName() . " = \$wrappedObject;\n" . '$instance->' . $prefixInterceptors->getName() . " = \$prefixInterceptors;\n" . '$instance->' . $suffixInterceptors->getName() . " = \$suffixInterceptors;\n\n" . 'return $instance;');
    }
}
