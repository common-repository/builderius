<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * The `staticProxyConstructor` implementation for lazy loading proxies
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class StaticProxyConstructor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Static constructor
     *
     * @param PropertyGenerator $initializerProperty
     * @param Properties        $properties
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\ProxyManager\ProxyGenerator\Util\Properties $properties)
    {
        parent::__construct('staticProxyConstructor', [], static::FLAG_PUBLIC | static::FLAG_STATIC);
        $this->setParameter(new \Builderius\Zend\Code\Generator\ParameterGenerator('initializer'));
        $this->setDocBlock("Constructor for lazy initialization\n\n@param \\Closure|null \$initializer");
        $this->setBody('static $reflection;' . "\n\n" . '$reflection = $reflection ?? $reflection = new \\ReflectionClass(__CLASS__);' . "\n" . '$instance = $reflection->newInstanceWithoutConstructor();' . "\n\n" . \Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator::generateSnippet($properties, 'instance') . '$instance->' . $initializerProperty->getName() . ' = $initializer;' . "\n\n" . 'return $instance;');
    }
}
