<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\LazyLoadingInterface::setProxyInitializer}
 * for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class SetProxyInitializer extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param PropertyGenerator $initializerProperty
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty)
    {
        parent::__construct('setProxyInitializer', [(new \Builderius\Zend\Code\Generator\ParameterGenerator('initializer', 'Closure'))->setDefaultValue(null)], self::FLAG_PUBLIC, '$this->' . $initializerProperty->getName() . ' = $initializer;');
    }
}
