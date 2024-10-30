<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use ReflectionClass;
/**
 * The `staticProxyConstructor` implementation for an access interceptor scope localizer proxy
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class StaticProxyConstructor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass $originalClass
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass)
    {
        parent::__construct('staticProxyConstructor', [], static::FLAG_PUBLIC | static::FLAG_STATIC);
        $localizedObject = new \Builderius\Zend\Code\Generator\ParameterGenerator('localizedObject');
        $prefix = new \Builderius\Zend\Code\Generator\ParameterGenerator('prefixInterceptors');
        $suffix = new \Builderius\Zend\Code\Generator\ParameterGenerator('suffixInterceptors');
        $localizedObject->setType($originalClass->getName());
        $prefix->setDefaultValue([]);
        $suffix->setDefaultValue([]);
        $prefix->setType('array');
        $suffix->setType('array');
        $this->setParameter($localizedObject);
        $this->setParameter($prefix);
        $this->setParameter($suffix);
        $this->setReturnType($originalClass->getName());
        $this->setDocBlock("Constructor to setup interceptors\n\n" . "@param \\" . $originalClass->getName() . " \$localizedObject\n" . "@param \\Closure[] \$prefixInterceptors method interceptors to be used before method logic\n" . "@param \\Closure[] \$suffixInterceptors method interceptors to be used before method logic\n\n" . '@return self');
        $this->setBody('static $reflection;' . "\n\n" . '$reflection = $reflection ?: $reflection = new \\ReflectionClass(__CLASS__);' . "\n" . '$instance   = $reflection->newInstanceWithoutConstructor();' . "\n\n" . '$instance->bindProxyProperties($localizedObject, $prefixInterceptors, $suffixInterceptors);' . "\n\n" . 'return $instance;');
    }
}
