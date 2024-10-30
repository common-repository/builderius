<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__set` for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicSet extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * @param \ReflectionClass                       $originalClass
     * @param \Zend\Code\Generator\PropertyGenerator $prefixInterceptors
     * @param \Zend\Code\Generator\PropertyGenerator $suffixInterceptors
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors)
    {
        parent::__construct($originalClass, '__set', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name'), new \Builderius\Zend\Code\Generator\ParameterGenerator('value')]);
        $parent = \Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists::get($originalClass, '__set');
        $callParent = '$returnValue = & parent::__set($name, $value);';
        if (!$parent) {
            $callParent = \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_SET, 'name', 'value', null, 'returnValue');
        }
        $this->setBody(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator::createInterceptedMethodBody($callParent, $this, $prefixInterceptors, $suffixInterceptors, $parent));
    }
}
