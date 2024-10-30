<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__sleep` for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicSleep extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass   $originalClass
     * @param PropertyGenerator $initializerProperty
     * @param MethodGenerator   $callInitializer
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\MethodGenerator $callInitializer)
    {
        parent::__construct($originalClass, '__sleep');
        $this->setBody('$this->' . $initializerProperty->getName() . ' && $this->' . $callInitializer->getName() . '(\'__sleep\', []);' . "\n\n" . ($originalClass->hasMethod('__sleep') ? 'return parent::__sleep();' : 'return array_keys((array) $this);'));
    }
}
