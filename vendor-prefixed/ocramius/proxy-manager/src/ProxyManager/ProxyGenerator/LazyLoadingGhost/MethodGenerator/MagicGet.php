<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializationTracker;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\MethodGenerator;
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
     * @var string
     */
    private $callParentTemplate = <<<'PHP'
$this->%s && ! $this->%s && $this->%s('__get', array('name' => $name));

if (isset(self::$%s[$name])) {
    return $this->$name;
}

if (isset(self::$%s[$name])) {
    if ($this->%s) {
        return $this->$name;
    }

    // check protected property access via compatible class
    $callers      = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
    $caller       = isset($callers[1]) ? $callers[1] : [];
    $object       = isset($caller['object']) ? $caller['object'] : '';
    $expectedType = self::$%s[$name];

    if ($object instanceof $expectedType) {
        return $this->$name;
    }

    $class = isset($caller['class']) ? $caller['class'] : '';

    if ($class === $expectedType || is_subclass_of($class, $expectedType) || $class === 'ReflectionProperty') {
        return $this->$name;
    }
} elseif (isset(self::$%s[$name])) {
    // check private property access via same class
    $callers = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
    $caller  = isset($callers[1]) ? $callers[1] : [];
    $class   = isset($caller['class']) ? $caller['class'] : '';

    static $accessorCache = [];

    if (isset(self::$%s[$name][$class])) {
        $cacheKey = $class . '#' . $name;
        $accessor = isset($accessorCache[$cacheKey])
            ? $accessorCache[$cacheKey]
            : $accessorCache[$cacheKey] = \Closure::bind(function & ($instance) use ($name) {
                return $instance->$name;
            }, null, $class);

        return $accessor($this);
    }

    if ($this->%s || 'ReflectionProperty' === $class) {
        $tmpClass = key(self::$%s[$name]);
        $cacheKey = $tmpClass . '#' . $name;
        $accessor = isset($accessorCache[$cacheKey])
            ? $accessorCache[$cacheKey]
            : $accessorCache[$cacheKey] = \Closure::bind(function & ($instance) use ($name) {
                return $instance->$name;
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
     * @param InitializationTracker  $initializationTracker
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $initializerProperty, \Builderius\Zend\Code\Generator\MethodGenerator $callInitializer, \Builderius\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap $publicProperties, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap $protectedProperties, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap $privateProperties, \Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializationTracker $initializationTracker)
    {
        parent::__construct($originalClass, '__get', [new \Builderius\Zend\Code\Generator\ParameterGenerator('name')]);
        $override = $originalClass->hasMethod('__get');
        $parentAccess = 'return parent::__get($name);';
        if (!$override) {
            $parentAccess = \Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::getPublicAccessSimulationCode(\Builderius\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator::OPERATION_GET, 'name');
        }
        $this->setBody(\sprintf($this->callParentTemplate, $initializerProperty->getName(), $initializationTracker->getName(), $callInitializer->getName(), $publicProperties->getName(), $protectedProperties->getName(), $initializationTracker->getName(), $protectedProperties->getName(), $privateProperties->getName(), $privateProperties->getName(), $initializationTracker->getName(), $privateProperties->getName(), $parentAccess));
    }
}
