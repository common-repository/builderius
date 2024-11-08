<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Executor\Promise;

use Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise;
use Builderius\GraphQL\Utils\Utils;
use Builderius\React\Promise\Promise as ReactPromise;
/**
 * Convenience wrapper for promises represented by Promise Adapter
 */
class Promise
{
    /** @var SyncPromise|ReactPromise */
    public $adoptedPromise;
    /** @var PromiseAdapter */
    private $adapter;
    /**
     * @param mixed $adoptedPromise
     */
    public function __construct($adoptedPromise, \Builderius\GraphQL\Executor\Promise\PromiseAdapter $adapter)
    {
        \Builderius\GraphQL\Utils\Utils::invariant(!$adoptedPromise instanceof self, 'Expecting promise from adapted system, got ' . self::class);
        $this->adapter = $adapter;
        $this->adoptedPromise = $adoptedPromise;
    }
    /**
     * @return Promise
     */
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
    {
        return $this->adapter->then($this, $onFulfilled, $onRejected);
    }
}
