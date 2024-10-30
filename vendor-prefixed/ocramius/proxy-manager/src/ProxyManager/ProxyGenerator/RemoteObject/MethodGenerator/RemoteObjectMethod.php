<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
use function var_export;
/**
 * Method decorator for remote objects
 */
class RemoteObjectMethod extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     *
     * @return self|static
     */
    public static function generateMethod(\Builderius\Zend\Code\Reflection\MethodReflection $originalMethod, \Builderius\Zend\Code\Generator\PropertyGenerator $adapterProperty, \ReflectionClass $originalClass) : self
    {
        /** @var self $method */
        $method = static::fromReflectionWithoutBodyAndDocBlock($originalMethod);
        $method->setBody('$return = $this->' . $adapterProperty->getName() . '->call(' . \var_export($originalClass->getName(), \true) . ', ' . \var_export($originalMethod->getName(), \true) . ', \\func_get_args());' . "\n\n" . \Builderius\ProxyManager\Generator\Util\ProxiedMethodReturnExpression::generate('$return', $originalMethod));
        return $method;
    }
}
