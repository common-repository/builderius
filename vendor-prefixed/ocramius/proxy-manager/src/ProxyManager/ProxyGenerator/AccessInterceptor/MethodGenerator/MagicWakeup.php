<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;
/**
 * Magic `__wakeup` for lazy loading value holder objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicWakeup extends \Builderius\ProxyManager\Generator\MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass $originalClass
     */
    public function __construct(\ReflectionClass $originalClass)
    {
        parent::__construct($originalClass, '__wakeup');
        $this->setBody(\Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator::generateSnippet(\Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass), 'this'));
    }
}
