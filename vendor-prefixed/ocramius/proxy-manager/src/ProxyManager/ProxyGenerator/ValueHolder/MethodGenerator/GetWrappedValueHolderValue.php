<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\ValueHolderInterface::getWrappedValueHolderValue}
 * for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class GetWrappedValueHolderValue extends \Builderius\ProxyManager\Generator\MethodGenerator
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
        parent::__construct('getWrappedValueHolderValue');
        $this->setBody('return $this->' . $valueHolderProperty->getName() . ';');
        $this->setReturnType('?object');
    }
}
