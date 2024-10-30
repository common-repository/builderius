<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * The `bindProxyProperties` method implementation for access interceptor scope localizers
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class BindProxyProperties extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass   $originalClass
     * @param PropertyGenerator $prefixInterceptors
     * @param PropertyGenerator $suffixInterceptors
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors)
    {
        parent::__construct('bindProxyProperties', [new \Builderius\Zend\Code\Generator\ParameterGenerator('localizedObject', $originalClass->getName()), new \Builderius\Zend\Code\Generator\ParameterGenerator('prefixInterceptors', 'array', []), new \Builderius\Zend\Code\Generator\ParameterGenerator('suffixInterceptors', 'array', [])], static::FLAG_PRIVATE, null, "@override constructor to setup interceptors\n\n" . "@param \\" . $originalClass->getName() . " \$localizedObject\n" . "@param \\Closure[] \$prefixInterceptors method interceptors to be used before method logic\n" . "@param \\Closure[] \$suffixInterceptors method interceptors to be used before method logic");
        $localizedProperties = [];
        $properties = \Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass);
        foreach ($properties->getAccessibleProperties() as $property) {
            $propertyName = $property->getName();
            $localizedProperties[] = '$this->' . $propertyName . ' = & $localizedObject->' . $propertyName . ';';
        }
        foreach ($properties->getPrivateProperties() as $property) {
            $propertyName = $property->getName();
            $localizedProperties[] = "\\Closure::bind(function () use (\$localizedObject) {\n    " . '$this->' . $propertyName . ' = & $localizedObject->' . $propertyName . ";\n" . '}, $this, ' . \var_export($property->getDeclaringClass()->getName(), \true) . ')->__invoke();';
        }
        $this->setBody(($localizedProperties ? \implode("\n\n", $localizedProperties) . "\n\n" : '') . '$this->' . $prefixInterceptors->getName() . " = \$prefixInterceptors;\n" . '$this->' . $suffixInterceptors->getName() . " = \$suffixInterceptors;");
    }
}
