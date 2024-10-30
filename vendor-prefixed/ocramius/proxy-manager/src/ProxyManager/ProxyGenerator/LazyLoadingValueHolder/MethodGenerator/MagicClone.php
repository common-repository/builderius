<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__clone` for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicClone extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass   $originalClass
     * @param PropertyGenerator $initializerProperty
     * @param PropertyGenerator $valueHolderProperty
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty)
    {
        parent::__construct($originalClass, '__clone');
        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();
        $this->setBody('$this->' . $initializer . ' && $this->' . $initializer . '->__invoke($this->' . $valueHolder . ', $this, \'__clone\', array(), $this->' . $initializer . ');' . "\n\n" . '$this->' . $valueHolder . ' = clone $this->' . $valueHolder . ';');
    }
}
