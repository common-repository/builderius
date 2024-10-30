<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Executor\Promise\Adapter;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Executor\ExecutionResult;
use Builderius\GraphQL\Executor\Promise\Promise;
use Builderius\GraphQL\Executor\Promise\PromiseAdapter;
use Builderius\GraphQL\Utils\Utils;
use Throwable;
use function count;
/**
 * Allows changing order of field resolution even in sync environments
 * (by leveraging queue of deferreds and promises)
 */
class SyncPromiseAdapter implements \Builderius\GraphQL\Executor\Promise\PromiseAdapter
{
    /**
     * @inheritdoc
     */
    public function isThenable($value)
    {
        return $value instanceof \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise;
    }
    /**
     * @inheritdoc
     */
    public function convertThenable($thenable)
    {
        if (!$thenable instanceof \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise) {
            // End-users should always use Deferred (and don't use SyncPromise directly)
            throw new \Builderius\GraphQL\Error\InvariantViolation('Expected instance of GraphQL\\Deferred, got ' . \Builderius\GraphQL\Utils\Utils::printSafe($thenable));
        }
        return new \Builderius\GraphQL\Executor\Promise\Promise($thenable, $this);
    }
    /**
     * @inheritdoc
     */
    public function then(\Builderius\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null)
    {
        /** @var SyncPromise $adoptedPromise */
        $adoptedPromise = $promise->adoptedPromise;
        return new \Builderius\GraphQL\Executor\Promise\Promise($adoptedPromise->then($onFulfilled, $onRejected), $this);
    }
    /**
     * @inheritdoc
     */
    public function create(callable $resolver)
    {
        $promise = new \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise();
        try {
            $resolver([$promise, 'resolve'], [$promise, 'reject']);
        } catch (\Throwable $e) {
            $promise->reject($e);
        }
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
    /**
     * @inheritdoc
     */
    public function createFulfilled($value = null)
    {
        $promise = new \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise();
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise->resolve($value), $this);
    }
    /**
     * @inheritdoc
     */
    public function createRejected($reason)
    {
        $promise = new \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise();
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise->reject($reason), $this);
    }
    /**
     * @inheritdoc
     */
    public function all(array $promisesOrValues)
    {
        $all = new \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise();
        $total = \count($promisesOrValues);
        $count = 0;
        $result = [];
        foreach ($promisesOrValues as $index => $promiseOrValue) {
            if ($promiseOrValue instanceof \Builderius\GraphQL\Executor\Promise\Promise) {
                $result[$index] = null;
                $promiseOrValue->then(static function ($value) use($index, &$count, $total, &$result, $all) : void {
                    $result[$index] = $value;
                    $count++;
                    if ($count < $total) {
                        return;
                    }
                    $all->resolve($result);
                }, [$all, 'reject']);
            } else {
                $result[$index] = $promiseOrValue;
                $count++;
            }
        }
        if ($count === $total) {
            $all->resolve($result);
        }
        return new \Builderius\GraphQL\Executor\Promise\Promise($all, $this);
    }
    /**
     * Synchronously wait when promise completes
     *
     * @return ExecutionResult
     */
    public function wait(\Builderius\GraphQL\Executor\Promise\Promise $promise)
    {
        $this->beforeWait($promise);
        $taskQueue = \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise::getQueue();
        while ($promise->adoptedPromise->state === \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise::PENDING && !$taskQueue->isEmpty()) {
            \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise::runQueue();
            $this->onWait($promise);
        }
        /** @var SyncPromise $syncPromise */
        $syncPromise = $promise->adoptedPromise;
        if ($syncPromise->state === \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise::FULFILLED) {
            return $syncPromise->result;
        }
        if ($syncPromise->state === \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise::REJECTED) {
            throw $syncPromise->result;
        }
        throw new \Builderius\GraphQL\Error\InvariantViolation('Could not resolve promise');
    }
    /**
     * Execute just before starting to run promise completion
     */
    protected function beforeWait(\Builderius\GraphQL\Executor\Promise\Promise $promise)
    {
    }
    /**
     * Execute while running promise completion
     */
    protected function onWait(\Builderius\GraphQL\Executor\Promise\Promise $promise)
    {
    }
}
