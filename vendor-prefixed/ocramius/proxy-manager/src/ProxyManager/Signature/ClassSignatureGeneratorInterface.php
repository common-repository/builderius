<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Signature;

use Builderius\Zend\Code\Generator\ClassGenerator;
/**
 * Applies a signature to a given class generator
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface ClassSignatureGeneratorInterface
{
    /**
     * Applies a signature to a given class generator
     */
    public function addSignature(\Builderius\Zend\Code\Generator\ClassGenerator $classGenerator, array $parameters) : \Builderius\Zend\Code\Generator\ClassGenerator;
}
