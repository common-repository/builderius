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
 * Magic `__get` for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicGet extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * @param ReflectionClass   $originalClass
     * @param PropertyGenerator $prefixInterceptors
     * @param PropertyGenerator $suffixInterceptors
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors)
    {
        parent::__construct($originalClass, '__get', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name')]);
        $parent = \Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists::get($originalClass, '__get');
        $callParent = '$returnValue = & parent::__get($name);';
        if (!$parent) {
            $callParent = \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_GET, 'name', null, null, 'returnValue');
        }
        $this->setBody(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator::createInterceptedMethodBody($callParent, $this, $prefixInterceptors, $suffixInterceptors, $parent));
    }
}
