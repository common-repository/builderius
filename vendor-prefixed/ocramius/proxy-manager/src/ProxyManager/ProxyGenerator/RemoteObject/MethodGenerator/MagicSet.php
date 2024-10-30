<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__set` for remote objects
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class MagicSet extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
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
        parent::__construct($originalClass, '__set', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name'), new \Builderius\Zend\Code\Generator\ParameterGenerator('value')]);
        $this->setDocBlock('@param string \\$name\\n@param mixed \\$value');
        $this->setBody('$return = $this->' . $adapterProperty->getName() . '->call(' . \var_export($originalClass->getName(), \true) . ', \'__set\', array($name, $value));' . "\n\n" . 'return $return;');
    }
}
