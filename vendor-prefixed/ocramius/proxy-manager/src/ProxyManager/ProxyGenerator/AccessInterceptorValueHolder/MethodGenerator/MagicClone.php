<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__clone` for lazy loading value holder objects
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
     * @param PropertyGenerator $valueHolderProperty
     * @param PropertyGenerator $prefixInterceptors
     * @param PropertyGenerator $suffixInterceptors
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty, \Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptors, \Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptors)
    {
        parent::__construct($originalClass, '__clone');
        $valueHolder = $valueHolderProperty->getName();
        $prefix = $prefixInterceptors->getName();
        $suffix = $suffixInterceptors->getName();
        $this->setBody("\$this->{$valueHolder} = clone \$this->{$valueHolder};\n\n" . "foreach (\$this->{$prefix} as \$key => \$value) {\n" . "    \$this->{$prefix}" . "[\$key] = clone \$value;\n" . "}\n\n" . "foreach (\$this->{$suffix} as \$key => \$value) {\n" . "    \$this->{$suffix}" . "[\$key] = clone \$value;\n" . '}');
    }
}
