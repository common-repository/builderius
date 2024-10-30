<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\GeneratorStrategy;

use Builderius\Zend\Code\Generator\ClassGenerator;
/**
 * Generator strategy interface - defines basic behavior of class generators
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface GeneratorStrategyInterface
{
    /**
     * Generate the provided class
     */
    public function generate(\Builderius\Zend\Code\Generator\ClassGenerator $classGenerator) : string;
}
