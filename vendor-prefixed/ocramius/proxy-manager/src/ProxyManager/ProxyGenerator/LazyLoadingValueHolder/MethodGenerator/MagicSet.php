<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__set` for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicSet extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass     $originalClass
     * @param PropertyGenerator   $initializerProperty
     * @param PropertyGenerator   $valueHolderProperty
     * @param PublicPropertiesMap $publicProperties
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty, \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap $publicProperties)
    {
        parent::__construct($originalClass, '__set', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name'), new \Builderius\Zend\Code\Generator\ParameterGenerator('value')]);
        $hasParent = $originalClass->hasMethod('__set');
        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();
        $callParent = '';
        if (!$publicProperties->isEmpty()) {
            $callParent = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n" . '    return ($this->' . $valueHolder . '->$name = $value);' . "\n}\n\n";
        }
        $callParent .= $hasParent ? 'return $this->' . $valueHolder . '->__set($name, $value);' : \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_SET, 'name', 'value', $valueHolderProperty);
        $this->setBody('$this->' . $initializer . ' && $this->' . $initializer . '->__invoke($this->' . $valueHolder . ', $this, ' . '\'__set\', array(\'name\' => $name, \'value\' => $value), $this->' . $initializer . ');' . "\n\n" . $callParent);
    }
}
