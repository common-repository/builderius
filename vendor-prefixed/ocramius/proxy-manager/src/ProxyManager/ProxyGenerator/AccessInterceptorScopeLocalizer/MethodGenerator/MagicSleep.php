<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__sleep` for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicSleep extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
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
        parent::__construct($originalClass, '__sleep');
        $parent = \Builderius\ProxyManager\ProxyGenerator\Util\GetMethodIfExists::get($originalClass, '__sleep');
        $callParent = $parent ? '$returnValue = & parent::__sleep();' : '$returnValue = array_keys((array) $this);';
        $this->setBody(\Builderius\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator::createInterceptedMethodBody($callParent, $this, $prefixInterceptors, $suffixInterceptors, $parent));
    }
}
