<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Signature;

use Builderius\ProxyManager\Signature\Exception\InvalidSignatureException;
use Builderius\ProxyManager\Signature\Exception\MissingSignatureException;
use ReflectionClass;
/**
 * Generator for signatures to be used to check the validity of generated code
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class SignatureChecker implements \Builderius\ProxyManager\Signature\SignatureCheckerInterface
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
     */
    public function checkSignature(\ReflectionClass $class, array $parameters)
    {
        $propertyName = 'signature' . $this->signatureGenerator->generateSignatureKey($parameters);
        $signature = $this->signatureGenerator->generateSignature($parameters);
        $defaultProperties = $class->getDefaultProperties();
        if (!\array_key_exists($propertyName, $defaultProperties)) {
            throw \Builderius\ProxyManager\Signature\Exception\MissingSignatureException::fromMissingSignature($class, $parameters, $signature);
        }
        if ($defaultProperties[$propertyName] !== $signature) {
            throw \Builderius\ProxyManager\Signature\Exception\InvalidSignatureException::fromInvalidSignature($class, $parameters, $defaultProperties[$propertyName], $signature);
        }
    }
}
