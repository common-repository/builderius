<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Generator\Util;

use ReflectionClass;
use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator;
/**
 * Util class to help to generate code
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 */
final class ClassGeneratorUtils
{
    public static function addMethodIfNotFinal(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\ClassGenerator $classGenerator, \Builderius\Zend\Code\Generator\MethodGenerator $generatedMethod) : bool
    {
        $methodName = $generatedMethod->getName();
        if ($originalClass->hasMethod($methodName) && $originalClass->getMethod($methodName)->isFinal()) {
            return \false;
        }
        $classGenerator->addMethodFromGenerator($generatedMethod);
        return \true;
    }
}
