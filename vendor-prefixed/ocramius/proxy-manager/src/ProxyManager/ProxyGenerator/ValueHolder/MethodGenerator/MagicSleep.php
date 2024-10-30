<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator;

use Builderius\ProxyManager\Generator\MagicMethodGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__sleep` for value holder objects
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
     * @param PropertyGenerator $valueHolderProperty
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $valueHolderProperty)
    {
        parent::__construct($originalClass, '__sleep');
        $this->setBody('return array(' . \var_export($valueHolderProperty->getName(), \true) . ');');
    }
}
