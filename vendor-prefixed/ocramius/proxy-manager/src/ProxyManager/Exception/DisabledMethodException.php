<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Exception;

use BadMethodCallException;
/**
 * Exception for forcefully disabled methods
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class DisabledMethodException extends \BadMethodCallException implements \Builderius\ProxyManager\Exception\ExceptionInterface
{
    const NAME = __CLASS__;
    public static function disabledMethod(string $method) : self
    {
        return new self(\sprintf('Method "%s" is forcefully disabled', $method));
    }
}
