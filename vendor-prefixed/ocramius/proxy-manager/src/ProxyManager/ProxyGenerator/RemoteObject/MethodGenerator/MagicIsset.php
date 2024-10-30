<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__isset` method for remote objects
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class MagicIsset extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     * @param ReflectionClass                        $originalClass
     * @param \Zend\Code\Generator\PropertyGenerator $adapterProperty
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $adapterProperty)
    {
        parent::__construct($originalClass, '__isset', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name')]);
        $this->setDocBlock('@param string $name');
        $this->setBody('$return = $this->' . $adapterProperty->getName() . '->call(' . \var_export($originalClass->getName(), \true) . ', \'__isset\', array($name));' . "\n\n" . 'return $return;');
    }
}
