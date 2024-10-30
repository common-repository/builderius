<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Signature;

use Builderius\Zend\Code\Generator\ClassGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Applies a signature to a given class generator
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class ClassSignatureGenerator implements \Builderius\ProxyManager\Signature\ClassSignatureGeneratorInterface
{
    /**
     * @var SignatureGeneratorInterface
     */
    private $signatureGenerator;
    /**
     * @param SignatureGeneratorInterface $signatureGenerator
     */
    public function __construct(\Builderius\ProxyManager\Signature\SignatureGeneratorInterface $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
    }
    /**
     * {@inheritDoc}
     *
     * @throws \Builderius\Zend\Code\Exception\InvalidArgumentException
     */
    public function addSignature(\Builderius\Zend\Code\Generator\ClassGenerator $classGenerator, array $parameters) : \Builderius\Zend\Code\Generator\ClassGenerator
    {
        $classGenerator->addPropertyFromGenerator(new \Builderius\Zend\Code\Generator\PropertyGenerator('signature' . $this->signatureGenerator->generateSignatureKey($parameters), $this->signatureGenerator->generateSignature($parameters), \Builderius\Zend\Code\Generator\PropertyGenerator::FLAG_STATIC | \Builderius\Zend\Code\Generator\PropertyGenerator::FLAG_PRIVATE));
        return $classGenerator;
    }
}
