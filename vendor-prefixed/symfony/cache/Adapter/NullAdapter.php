<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Cache\Adapter;

use Builderius\Psr\Cache\CacheItemInterface;
use Builderius\Symfony\Component\Cache\CacheItem;
use Builderius\Symfony\Contracts\Cache\CacheInterface;
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class NullAdapter implements \Builderius\Symfony\Component\Cache\Adapter\AdapterInterface, \Builderius\Symfony\Contracts\Cache\CacheInterface
{
    private $createCacheItem;
    public function __construct()
    {
        $this->createCacheItem = \Closure::bind(function ($key) {
            $item = new \Builderius\Symfony\Component\Cache\CacheItem();
            $item->key = $key;
            $item->isHit = \false;
            return $item;
        }, $this, \Builderius\Symfony\Component\Cache\CacheItem::class);
    }
    /**
     * {@inheritdoc}
     */
    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null)
    {
        $save = \true;
        return $callback(($this->createCacheItem)($key), $save);
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $f = $this->createCacheItem;
        return $f($key);
    }
    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        return $this->generateItems($keys);
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function clear(string $prefix = '')
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(\Builderius\Psr\Cache\CacheItemInterface $item)
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(\Builderius\Psr\Cache\CacheItemInterface $item)
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function commit()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function delete(string $key) : bool
    {
        return $this->deleteItem($key);
    }
    private function generateItems(array $keys)
    {
        $f = $this->createCacheItem;
        foreach ($keys as $key) {
            (yield $key => $f($key));
        }
    }
}
