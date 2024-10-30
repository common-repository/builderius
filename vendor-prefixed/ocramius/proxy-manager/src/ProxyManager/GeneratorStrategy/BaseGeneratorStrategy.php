<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\GeneratorStrategy;

use Builderius\Zend\Code\Generator\ClassGenerator;
/**
 * Generator strategy that generates the class body
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class BaseGeneratorStrategy implements \Builderius\ProxyManager\GeneratorStrategy\GeneratorStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(\Builderius\Zend\Code\Generator\ClassGenerator $classGenerator) : string
    {
        return $classGenerator->generate();
    }
}
