<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\Util;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Utility to create pre- and post- method interceptors around a given method body
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @private - this class is just here as a small utility for this component, don't use it in your own code
 */
class InterceptorGenerator
{
    /**
     * @param string                                  $methodBody         the body of the previously generated code.
     *                                                                    It MUST assign the return value to a variable
     *                                                                    `$returnValue` instead of directly returning
     * @param \Builderius\ProxyManager\Generator\MethodGenerator $method
     * @param \Zend\Code\Generator\PropertyGenerator  $valueHolder
     * @param \Zend\Code\Generator\PropertyGenerator  $prefixInterceptors
     * @param \Zend\Code\Generator\PropertyGenerator  $suffixInterceptors
     * @param \ReflectionMethod|null                  $originalMethod
     */
    public static function createInterceptedMethodBody(string $methodBody, \Builderius\ProxyManager\Generator\MethodGenerator $method, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolder, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors, ?\ReflectionMethod $originalMethod) : string
    {
        $name = \var_export($method->getName(), \true);
        $valueHolderName = $valueHolder->getName();
        $prefixInterceptorsName = $prefixInterceptors->getName();
        $suffixInterceptorsName = $suffixInterceptors->getName();
        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $params[] = \var_export($parameterName, \true) . ' => $' . $parameter->getName();
        }
        $paramsString = 'array(' . \implode(', ', $params) . ')';
        return "if (isset(\$this->{$prefixInterceptorsName}" . "[{$name}])) {\n" . "    \$returnEarly       = false;\n" . "    \$prefixReturnValue = \$this->{$prefixInterceptorsName}" . "[{$name}]->__invoke(" . "\$this, \$this->{$valueHolderName}, {$name}, {$paramsString}, \$returnEarly);\n\n" . "    if (\$returnEarly) {\n" . '        ' . \Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression::generate('$prefixReturnValue', $originalMethod) . "\n" . "    }\n" . "}\n\n" . $methodBody . "\n\n" . "if (isset(\$this->{$suffixInterceptorsName}" . "[{$name}])) {\n" . "    \$returnEarly       = false;\n" . "    \$suffixReturnValue = \$this->{$suffixInterceptorsName}" . "[{$name}]->__invoke(" . "\$this, \$this->{$valueHolderName}, {$name}, {$paramsString}, \$returnValue, \$returnEarly);\n\n" . "    if (\$returnEarly) {\n" . '        ' . \Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression::generate('$suffixReturnValue', $originalMethod) . "\n" . "    }\n" . "}\n\n" . \Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression::generate('$returnValue', $originalMethod);
    }
}
