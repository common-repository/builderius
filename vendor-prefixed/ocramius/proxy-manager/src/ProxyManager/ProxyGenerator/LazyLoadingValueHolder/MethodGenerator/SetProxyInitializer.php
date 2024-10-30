<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use Closure;
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
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty)
    {
        parent::__construct('setProxyInitializer');
        $initializerParameter = new \Builderius\Zend\Code\Generator\ParameterGenerator('initializer');
        $initializerParameter->setType(\Closure::class);
        $initializerParameter->setDefaultValue(null);
        $this->setParameter($initializerParameter);
        $this->setBody('$this->' . $initializerProperty->getName() . ' = $initializer;');
    }
}
