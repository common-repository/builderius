<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Generator;

use Builderius\Zend\Code\Generator\MethodGenerator as ZendMethodGenerator;
use Builderius\Zend\Code\Reflection\MethodReflection;
/**
 * Method generator that fixes minor quirks in ZF2's method generator
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MethodGenerator extends \Builderius\Zend\Code\Generator\MethodGenerator
{
    /**
     * {@inheritDoc}
     */
    public static function fromReflectionWithoutBodyAndDocBlock(\Builderius\Zend\Code\Reflection\MethodReflection $reflectionMethod) : self
    {
        /* @var $method self */
        $method = parent::copyMethodSignature($reflectionMethod);
        $method->setInterface(\false);
        return $method;
    }
}
