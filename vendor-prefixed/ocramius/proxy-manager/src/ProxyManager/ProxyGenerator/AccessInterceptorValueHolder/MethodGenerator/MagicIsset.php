<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\Util\InterceptorGenerator;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__isset` for method interceptor value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicIsset extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     * @param ReflectionClass     $originalClass
     * @param PropertyGenerator   $valueHolder
     * @param PropertyGenerator   $prefixInterceptors
     * @param PropertyGenerator   $suffixInterceptors
     * @param PublicPropertiesMap $publicProperties
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolder, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors, \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap $publicProperties)
    {
        parent::__construct($originalClass, '__isset', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name')]);
        $parent = \Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists::get($originalClass, '__isset');
        $valueHolderName = $valueHolder->getName();
        $callParent = \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_ISSET, 'name', 'value', $valueHolder, 'returnValue');
        if (!$publicProperties->isEmpty()) {
            $callParent = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n" . '    $returnValue = isset($this->' . $valueHolderName . '->$name);' . "\n} else {\n    {$callParent}\n}\n\n";
        }
        $this->setBody(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\Util\InterceptorGenerator::createInterceptedMethodBody($callParent, $this, $valueHolder, $prefixInterceptors, $suffixInterceptors, $parent));
    }
}
