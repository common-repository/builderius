<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__clone` for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicClone extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass   $originalClass
     * @param PropertyGenerator $prefixInterceptors
     * @param PropertyGenerator $suffixInterceptors
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors)
    {
        parent::__construct($originalClass, '__clone');
        $parent = \Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists::get($originalClass, '__clone');
        $this->setBody(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator::createInterceptedMethodBody($parent ? '$returnValue = parent::__clone();' : '$returnValue = null;', $this, $prefixInterceptors, $suffixInterceptors, $parent));
    }
}
