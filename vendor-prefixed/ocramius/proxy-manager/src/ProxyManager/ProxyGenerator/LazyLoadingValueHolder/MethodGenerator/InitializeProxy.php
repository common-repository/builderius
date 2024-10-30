<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\LazyLoadingInterface::initializeProxy}
 * for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InitializeProxy extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param PropertyGenerator $initializerProperty
     * @param PropertyGenerator $valueHolderProperty
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty)
    {
        parent::__construct('initializeProxy');
        $this->setReturnType('bool');
        $initializer = $initializerProperty->getName();
        $this->setBody('return $this->' . $initializer . ' && $this->' . $initializer . '->__invoke($this->' . $valueHolderProperty->getName() . ', $this, \'initializeProxy\', array(), $this->' . $initializer . ');');
    }
}
