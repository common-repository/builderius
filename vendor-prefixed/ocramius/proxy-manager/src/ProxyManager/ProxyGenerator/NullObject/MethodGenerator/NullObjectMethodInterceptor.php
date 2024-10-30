<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\NullObject\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\Generator\Util\IdentifierSuffixer;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Method decorator for null objects
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class NullObjectMethodInterceptor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * @param \Zend\Code\Reflection\MethodReflection $originalMethod
     *
     * @return self|static
     */
    public static function generateMethod(\Builderius\Zend\Code\Reflection\MethodReflection $originalMethod) : self
    {
        /* @var $method self */
        $method = static::fromReflectionWithoutBodyAndDocBlock($originalMethod);
        if ($originalMethod->returnsReference()) {
            $reference = \Builderius\ProxyManager\Generator\Util\IdentifierSuffixer::getIdentifier('ref');
            $method->setBody("\${$reference} = null;\nreturn \${$reference};");
        }
        return $method;
    }
}
