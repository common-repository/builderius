<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Method with additional pre- and post- interceptor logic in the body
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InterceptedMethod extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public static function generateMethod(\Builderius\Zend\Code\Reflection\MethodReflection $originalMethod, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors) : self
    {
        /* @var $method self */
        $method = static::fromReflectionWithoutBodyAndDocBlock($originalMethod);
        $forwardedParams = [];
        foreach ($originalMethod->getParameters() as $parameter) {
            $forwardedParams[] = ($parameter->isVariadic() ? '...' : '') . '$' . $parameter->getName();
        }
        $method->setBody(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator::createInterceptedMethodBody('$returnValue = parent::' . $originalMethod->getName() . '(' . \implode(', ', $forwardedParams) . ');', $method, $prefixInterceptors, $suffixInterceptors, $originalMethod));
        return $method;
    }
}
