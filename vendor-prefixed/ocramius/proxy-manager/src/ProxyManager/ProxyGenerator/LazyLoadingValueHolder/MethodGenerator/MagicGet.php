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
 * Magic `__get` for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicGet extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
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
        parent::__construct($originalClass, '__get', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name')]);
        $hasParent = $originalClass->hasMethod('__get');
        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();
        $callParent = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n" . '    return $this->' . $valueHolder . '->$name;' . "\n}\n\n";
        if ($hasParent) {
            $this->setInitializerBody($initializer, $valueHolder, $callParent . 'return $this->' . $valueHolder . '->__get($name);');
            return;
        }
        $this->setInitializerBody($initializer, $valueHolder, $callParent . \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_GET, 'name', null, $valueHolderProperty));
    }
    private function setInitializerBody(string $initializer, string $valueHolder, string $callParent) : void
    {
        $this->setBody('$this->' . $initializer . ' && $this->' . $initializer . '->__invoke($this->' . $valueHolder . ', $this, \'__get\', [\'name\' => $name], $this->' . $initializer . ');' . "\n\n" . $callParent);
    }
}
