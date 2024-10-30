<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Cache;

use Builderius\Psr\Log\LoggerInterface;
use Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException;
use Builderius\Symfony\Component\Cache\Exception\LogicException;
use Builderius\Symfony\Contracts\Cache\ItemInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class CacheItem implements \Builderius\Symfony\Contracts\Cache\ItemInterface
{
    private const METADATA_EXPIRY_OFFSET = 1527506807;
    protected $key;
    protected $value;
    protected $isHit = \false;
    protected $expiry;
    protected $metadata = [];
    protected $newMetadata = [];
    protected $innerItem;
    protected $poolHash;
    protected $isTaggable = \false;
    /**
     * {@inheritdoc}
     */
    public function getKey() : string
    {
        return $this->key;
    }
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->value;
    }
    /**
     * {@inheritdoc}
     */
    public function isHit() : bool
    {
        return $this->isHit;
    }
    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function set($value) : self
    {
        $this->value = $value;
        return $this;
    }
    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function expiresAt($expiration) : self
    {
        if (null === $expiration) {
            $this->expiry = null;
        } elseif ($expiration instanceof \DateTimeInterface) {
            $this->expiry = (float) $expiration->format('U.u');
        } else {
            throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Expiration date must implement DateTimeInterface or be null, "%s" given.', \is_object($expiration) ? \get_class($expiration) : \gettype($expiration)));
        }
        return $this;
    }
    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function expiresAfter($time) : self
    {
        if (null === $time) {
            $this->expiry = null;
        } elseif ($time instanceof \DateInterval) {
            $this->expiry = \microtime(\true) + \DateTime::createFromFormat('U', 0)->add($time)->format('U.u');
        } elseif (\is_int($time)) {
            $this->expiry = $time + \microtime(\true);
        } else {
            throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Expiration date must be an integer, a DateInterval or null, "%s" given.', \is_object($time) ? \get_class($time) : \gettype($time)));
        }
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function tag($tags) : \Builderius\Symfony\Contracts\Cache\ItemInterface
    {
        if (!$this->isTaggable) {
            throw new \Builderius\Symfony\Component\Cache\Exception\LogicException(\sprintf('Cache item "%s" comes from a non tag-aware pool: you cannot tag it.', $this->key));
        }
        if (!\is_iterable($tags)) {
            $tags = [$tags];
        }
        foreach ($tags as $tag) {
            if (!\is_string($tag)) {
                throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Cache tag must be string, "%s" given.', \is_object($tag) ? \get_class($tag) : \gettype($tag)));
            }
            if (isset($this->newMetadata[self::METADATA_TAGS][$tag])) {
                continue;
            }
            if ('' === $tag) {
                throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException('Cache tag length must be greater than zero.');
            }
            if (\false !== \strpbrk($tag, self::RESERVED_CHARACTERS)) {
                throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Cache tag "%s" contains reserved characters "%s".', $tag, self::RESERVED_CHARACTERS));
            }
            $this->newMetadata[self::METADATA_TAGS][$tag] = $tag;
        }
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getMetadata() : array
    {
        return $this->metadata;
    }
    /**
     * Validates a cache key according to PSR-6.
     *
     * @param string $key The key to validate
     *
     * @throws InvalidArgumentException When $key is not valid
     */
    public static function validateKey($key) : string
    {
        if (!\is_string($key)) {
            throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Cache key must be string, "%s" given.', \is_object($key) ? \get_class($key) : \gettype($key)));
        }
        if ('' === $key) {
            throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException('Cache key length must be greater than zero.');
        }
        if (\false !== \strpbrk($key, self::RESERVED_CHARACTERS)) {
            throw new \Builderius\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Cache key "%s" contains reserved characters "%s".', $key, self::RESERVED_CHARACTERS));
        }
        return $key;
    }
    /**
     * Internal logging helper.
     *
     * @internal
     */
    public static function log(?\Builderius\Psr\Log\LoggerInterface $logger, string $message, array $context = [])
    {
        if ($logger) {
            $logger->warning($message, $context);
        } else {
            $replace = [];
            foreach ($context as $k => $v) {
                if (\is_scalar($v)) {
                    $replace['{' . $k . '}'] = $v;
                }
            }
            @\trigger_error(\strtr($message, $replace), \E_USER_WARNING);
        }
    }
}
