<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\MethodGenerator as ZendMethodGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\LazyLoadingInterface::initializeProxy}
 * for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InitializeProxy extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param PropertyGenerator   $initializerProperty
     * @param ZendMethodGenerator $callInitializer
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\MethodGenerator $callInitializer)
    {
        parent::__construct('initializeProxy');
        $this->setReturnType('bool');
        $this->setBody('return $this->' . $initializerProperty->getName() . ' && $this->' . $callInitializer->getName() . '(\'initializeProxy\', []);');
    }
}
