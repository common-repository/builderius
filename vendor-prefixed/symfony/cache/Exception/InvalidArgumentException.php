<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Cache\Exception;

use Builderius\Psr\Cache\InvalidArgumentException as Psr6CacheInterface;
use Builderius\Psr\SimpleCache\InvalidArgumentException as SimpleCacheInterface;
if (\interface_exists(\Builderius\Psr\SimpleCache\InvalidArgumentException::class)) {
    class InvalidArgumentException extends \InvalidArgumentException implements \Builderius\Psr\Cache\InvalidArgumentException, \Builderius\Psr\SimpleCache\InvalidArgumentException
    {
    }
} else {
    class InvalidArgumentException extends \InvalidArgumentException implements \Builderius\Psr\Cache\InvalidArgumentException
    {
    }
}
