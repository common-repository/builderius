<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\LazyLoadingInterface::isProxyInitialized}
 * for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class IsProxyInitialized extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param PropertyGenerator $valueHolderProperty
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty)
    {
        parent::__construct('isProxyInitialized');
        $this->setReturnType('bool');
        $this->setBody('return null !== $this->' . $valueHolderProperty->getName() . ';');
    }
}
