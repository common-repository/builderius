<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Signature\Exception;

use ReflectionClass;
use UnexpectedValueException;
/**
 * Exception for invalid provided signatures
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InvalidSignatureException extends \UnexpectedValueException implements \Builderius\ProxyManager\Signature\Exception\ExceptionInterface
{
    public static function fromInvalidSignature(\ReflectionClass $class, array $parameters, string $signature, string $expected) : self
    {
        return new self(\sprintf('Found signature "%s" for class "%s" does not correspond to expected signature "%s" for %d parameters', $signature, $class->getName(), $expected, \count($parameters)));
    }
}
