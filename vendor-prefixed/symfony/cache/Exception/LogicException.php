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

use Builderius\Psr\Cache\CacheException as Psr6CacheInterface;
use Builderius\Psr\SimpleCache\CacheException as SimpleCacheInterface;
if (\interface_exists(\Builderius\Psr\SimpleCache\CacheException::class)) {
    class LogicException extends \LogicException implements \Builderius\Psr\Cache\CacheException, \Builderius\Psr\SimpleCache\CacheException
    {
    }
} else {
    class LogicException extends \LogicException implements \Builderius\Psr\Cache\CacheException
    {
    }
}
