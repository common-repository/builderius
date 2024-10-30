<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__isset` method for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicIsset extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * @var string
     */
    private $callParentTemplate = <<<'PHP'
%s

if (isset(self::$%s[$name])) {
    return isset($this->$name);
}

if (isset(self::$%s[$name])) {
    // check protected property access via compatible class
    $callers      = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
    $caller       = isset($callers[1]) ? $callers[1] : [];
    $object       = isset($caller['object']) ? $caller['object'] : '';
    $expectedType = self::$%s[$name];

    if ($object instanceof $expectedType) {
        return isset($this->$name);
    }

    $class = isset($caller['class']) ? $caller['class'] : '';

    if ($class === $expectedType || is_subclass_of($class, $expectedType)) {
        return isset($this->$name);
    }
} else {
    // check private property access via same class
    $callers = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
    $caller  = isset($callers[1]) ? $callers[1] : [];
    $class   = isset($caller['class']) ? $caller['class'] : '';

    static $accessorCache = [];

    if (isset(self::$%s[$name][$class])) {
        $cacheKey = $class . '#' . $name;
        $accessor = isset($accessorCache[$cacheKey])
            ? $accessorCache[$cacheKey]
            : $accessorCache[$cacheKey] = \Closure::bind(function ($instance) use ($name) {
                return isset($instance->$name);
            }, null, $class);

        return $accessor($this);
    }

    if ('ReflectionProperty' === $class) {
        $tmpClass = key(self::$%s[$name]);
        $cacheKey = $tmpClass . '#' . $name;
        $accessor = isset($accessorCache[$cacheKey])
            ? $accessorCache[$cacheKey]
            : $accessorCache[$cacheKey] = \Closure::bind(function ($instance) use ($name) {
                return isset($instance->$name);
            }, null, $tmpClass);

        return $accessor($this);
    }
}

%s
PHP;
    /**
     * @param ReflectionClass        $originalClass
     * @param PropertyGenerator      $initializerProperty
     * @param MethodGenerator        $callInitializer
     * @param PublicPropertiesMap    $publicProperties
     * @param ProtectedPropertiesMap $protectedProperties
     * @param PrivatePropertiesMap   $privateProperties
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\MethodGenerator $callInitializer, \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap $publicProperties, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap $protectedProperties, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap $privateProperties)
    {
        parent::__construct($originalClass, '__isset', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name')]);
        $override = $originalClass->hasMethod('__isset');
        $parentAccess = 'return parent::__isset($name);';
        if (!$override) {
            $parentAccess = \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_ISSET, 'name');
        }
        $this->setBody(\sprintf($this->callParentTemplate, '$this->' . $initializerProperty->getName() . ' && $this->' . $callInitializer->getName() . '(\'__isset\', array(\'name\' => $name));', $publicProperties->getName(), $protectedProperties->getName(), $protectedProperties->getName(), $privateProperties->getName(), $privateProperties->getName(), $parentAccess));
    }
}
