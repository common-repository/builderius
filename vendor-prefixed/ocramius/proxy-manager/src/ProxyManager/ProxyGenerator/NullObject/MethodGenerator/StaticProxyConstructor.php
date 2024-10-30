<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\NullObject\MethodGenerator;

use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use ReflectionClass;
use ReflectionProperty;
/**
 * The `staticProxyConstructor` implementation for null object proxies
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class StaticProxyConstructor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass $originalClass Reflection of the class to proxy
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass)
    {
        parent::__construct('staticProxyConstructor', [], static::FLAG_PUBLIC | static::FLAG_STATIC);
        $nullableProperties = \array_map(function (\ReflectionProperty $publicProperty) : string {
            return '$instance->' . $publicProperty->getName() . ' = null;';
        }, \Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass)->getPublicProperties());
        $this->setDocBlock('Constructor for null object initialization');
        $this->setBody('static $reflection;' . "\n\n" . '$reflection = $reflection ?? $reflection = new \\ReflectionClass(__CLASS__);' . "\n" . '$instance = $reflection->newInstanceWithoutConstructor();' . "\n\n" . ($nullableProperties ? \implode("\n", $nullableProperties) . "\n\n" : '') . 'return $instance;');
    }
}
