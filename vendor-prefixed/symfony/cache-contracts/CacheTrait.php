<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Contracts\Cache;

use Builderius\Psr\Cache\CacheItemPoolInterface;
use Builderius\Psr\Cache\InvalidArgumentException;
use Builderius\Psr\Log\LoggerInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\Builderius\Psr\Cache\InvalidArgumentException::class);
/**
 * An implementation of CacheInterface for PSR-6 CacheItemPoolInterface classes.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait CacheTrait
{
    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null)
    {
        return $this->doGet($this, $key, $callback, $beta, $metadata);
    }
    /**
     * {@inheritdoc}
     */
    public function delete(string $key) : bool
    {
        return $this->deleteItem($key);
    }
    private function doGet(\Builderius\Psr\Cache\CacheItemPoolInterface $pool, string $key, callable $callback, ?float $beta, array &$metadata = null, \Builderius\Psr\Log\LoggerInterface $logger = null)
    {
        if (0 > ($beta = $beta ?? 1.0)) {
            throw new class(\sprintf('Argument "$beta" provided to "%s::get()" must be a positive number, %f given.', static::class, $beta)) extends \InvalidArgumentException implements \Builderius\Psr\Cache\InvalidArgumentException
            {
            };
        }
        $item = $pool->getItem($key);
        $recompute = !$item->isHit() || \INF === $beta;
        $metadata = $item instanceof \Builderius\Symfony\Contracts\Cache\ItemInterface ? $item->getMetadata() : [];
        if (!$recompute && $metadata) {
            $expiry = $metadata[\Builderius\Symfony\Contracts\Cache\ItemInterface::METADATA_EXPIRY] ?? \false;
            $ctime = $metadata[\Builderius\Symfony\Contracts\Cache\ItemInterface::METADATA_CTIME] ?? \false;
            if ($recompute = $ctime && $expiry && $expiry <= ($now = \microtime(\true)) - $ctime / 1000 * $beta * \log(\random_int(1, \PHP_INT_MAX) / \PHP_INT_MAX)) {
                // force applying defaultLifetime to expiry
                $item->expiresAt(null);
                $logger && $logger->info('Item "{key}" elected for early recomputation {delta}s before its expiration', ['key' => $key, 'delta' => \sprintf('%.1f', $expiry - $now)]);
            }
        }
        if ($recompute) {
            $save = \true;
            $item->set($callback($item, $save));
            if ($save) {
                $pool->save($item);
            }
        }
        return $item->get();
    }
}
