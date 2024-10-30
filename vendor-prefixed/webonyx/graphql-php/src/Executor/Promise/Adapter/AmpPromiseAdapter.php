<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Executor\Promise\Adapter;

use Builderius\Amp\Deferred;
use Builderius\Amp\Failure;
use Builderius\Amp\Promise as AmpPromise;
use Builderius\Amp\Success;
use Builderius\GraphQL\Executor\Promise\Promise;
use Builderius\GraphQL\Executor\Promise\PromiseAdapter;
use Throwable;
use function Builderius\Amp\Promise\all;
use function array_replace;
class AmpPromiseAdapter implements \Builderius\GraphQL\Executor\Promise\PromiseAdapter
{
    /**
     * @inheritdoc
     */
    public function isThenable($value) : bool
    {
        return $value instanceof \Builderius\Amp\Promise;
    }
    /**
     * @inheritdoc
     */
    public function convertThenable($thenable) : \Builderius\GraphQL\Executor\Promise\Promise
    {
        return new \Builderius\GraphQL\Executor\Promise\Promise($thenable, $this);
    }
    /**
     * @inheritdoc
     */
    public function then(\Builderius\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null) : \Builderius\GraphQL\Executor\Promise\Promise
    {
        $deferred = new \Builderius\Amp\Deferred();
        $onResolve = static function (?\Throwable $reason, $value) use($onFulfilled, $onRejected, $deferred) : void {
            if ($reason === null && $onFulfilled !== null) {
                self::resolveWithCallable($deferred, $onFulfilled, $value);
            } elseif ($reason === null) {
                $deferred->resolve($value);
            } elseif ($onRejected !== null) {
                self::resolveWithCallable($deferred, $onRejected, $reason);
            } else {
                $deferred->fail($reason);
            }
        };
        /** @var AmpPromise $adoptedPromise */
        $adoptedPromise = $promise->adoptedPromise;
        $adoptedPromise->onResolve($onResolve);
        return new \Builderius\GraphQL\Executor\Promise\Promise($deferred->promise(), $this);
    }
    /**
     * @inheritdoc
     */
    public function create(callable $resolver) : \Builderius\GraphQL\Executor\Promise\Promise
    {
        $deferred = new \Builderius\Amp\Deferred();
        $resolver(static function ($value) use($deferred) : void {
            $deferred->resolve($value);
        }, static function (\Throwable $exception) use($deferred) : void {
            $deferred->fail($exception);
        });
        return new \Builderius\GraphQL\Executor\Promise\Promise($deferred->promise(), $this);
    }
    /**
     * @inheritdoc
     */
    public function createFulfilled($value = null) : \Builderius\GraphQL\Executor\Promise\Promise
    {
        $promise = new \Builderius\Amp\Success($value);
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
    /**
     * @inheritdoc
     */
    public function createRejected($reason) : \Builderius\GraphQL\Executor\Promise\Promise
    {
        $promise = new \Builderius\Amp\Failure($reason);
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
    /**
     * @inheritdoc
     */
    public function all(array $promisesOrValues) : \Builderius\GraphQL\Executor\Promise\Promise
    {
        /** @var AmpPromise[] $promises */
        $promises = [];
        foreach ($promisesOrValues as $key => $item) {
            if ($item instanceof \Builderius\GraphQL\Executor\Promise\Promise) {
                $promises[$key] = $item->adoptedPromise;
            } elseif ($item instanceof \Builderius\Amp\Promise) {
                $promises[$key] = $item;
            }
        }
        $deferred = new \Builderius\Amp\Deferred();
        $onResolve = static function (?\Throwable $reason, ?array $values) use($promisesOrValues, $deferred) : void {
            if ($reason === null) {
                $deferred->resolve(\array_replace($promisesOrValues, $values));
                return;
            }
            $deferred->fail($reason);
        };
        \Builderius\Amp\Promise\all($promises)->onResolve($onResolve);
        return new \Builderius\GraphQL\Executor\Promise\Promise($deferred->promise(), $this);
    }
    private static function resolveWithCallable(\Builderius\Amp\Deferred $deferred, callable $callback, $argument) : void
    {
        try {
            $result = $callback($argument);
        } catch (\Throwable $exception) {
            $deferred->fail($exception);
            return;
        }
        if ($result instanceof \Builderius\GraphQL\Executor\Promise\Promise) {
            $result = $result->adoptedPromise;
        }
        $deferred->resolve($result);
    }
}
