<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Signature;

use Builderius\ProxyManager\Inflector\Util\ParameterEncoder;
use Builderius\ProxyManager\Inflector\Util\ParameterHasher;
/**
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class SignatureGenerator implements \Builderius\ProxyManager\Signature\SignatureGeneratorInterface
{
    /**
     * @var ParameterEncoder
     */
    private $parameterEncoder;
    /**
     * @var ParameterHasher
     */
    private $parameterHasher;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parameterEncoder = new \Builderius\ProxyManager\Inflector\Util\ParameterEncoder();
        $this->parameterHasher = new \Builderius\ProxyManager\Inflector\Util\ParameterHasher();
    }
    /**
     * {@inheritDoc}
     */
    public function generateSignature(array $parameters) : string
    {
        return $this->parameterEncoder->encodeParameters($parameters);
    }
    /**
     * {@inheritDoc}
     */
    public function generateSignatureKey(array $parameters) : string
    {
        return $this->parameterHasher->hashParameters($parameters);
    }
}
