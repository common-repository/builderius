<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression;
use Builderius\Zend\Code\Generator\PropertyGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Method decorator for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class LazyLoadingMethodInterceptor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public static function generateMethod(\Builderius\Zend\Code\Reflection\MethodReflection $originalMethod, \Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty) : self
    {
        /* @var $method self */
        $method = static::fromReflectionWithoutBodyAndDocBlock($originalMethod);
        $initializerName = $initializerProperty->getName();
        $valueHolderName = $valueHolderProperty->getName();
        $parameters = $originalMethod->getParameters();
        $methodName = $originalMethod->getName();
        $initializerParams = [];
        $forwardedParams = [];
        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $variadicPrefix = $parameter->isVariadic() ? '...' : '';
            $initializerParams[] = \var_export($parameterName, \true) . ' => $' . $parameterName;
            $forwardedParams[] = $variadicPrefix . '$' . $parameterName;
        }
        $method->setBody('$this->' . $initializerName . ' && $this->' . $initializerName . '->__invoke($this->' . $valueHolderName . ', $this, ' . \var_export($methodName, \true) . ', array(' . \implode(', ', $initializerParams) . '), $this->' . $initializerName . ");\n\n" . \Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression::generate('$this->' . $valueHolderName . '->' . $methodName . '(' . \implode(', ', $forwardedParams) . ')', $originalMethod));
        return $method;
    }
}
