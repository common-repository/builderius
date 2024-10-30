<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Executor\Promise\Adapter;

use Builderius\GraphQL\Executor\Promise\Promise;
use Builderius\GraphQL\Executor\Promise\PromiseAdapter;
use Builderius\GraphQL\Utils\Utils;
use Builderius\React\Promise\Promise as ReactPromise;
use Builderius\React\Promise\PromiseInterface as ReactPromiseInterface;
use function Builderius\React\Promise\all;
use function Builderius\React\Promise\reject;
use function Builderius\React\Promise\resolve;
class ReactPromiseAdapter implements \Builderius\GraphQL\Executor\Promise\PromiseAdapter
{
    /**
     * @inheritdoc
     */
    public function isThenable($value)
    {
        return $value instanceof \Builderius\React\Promise\PromiseInterface;
    }
    /**
     * @inheritdoc
     */
    public function convertThenable($thenable)
    {
        return new \Builderius\GraphQL\Executor\Promise\Promise($thenable, $this);
    }
    /**
     * @inheritdoc
     */
    public function then(\Builderius\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null)
    {
        /** @var ReactPromiseInterface $adoptedPromise */
        $adoptedPromise = $promise->adoptedPromise;
        return new \Builderius\GraphQL\Executor\Promise\Promise($adoptedPromise->then($onFulfilled, $onRejected), $this);
    }
    /**
     * @inheritdoc
     */
    public function create(callable $resolver)
    {
        $promise = new \Builderius\React\Promise\Promise($resolver);
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
    /**
     * @inheritdoc
     */
    public function createFulfilled($value = null)
    {
        $promise = \Builderius\React\Promise\resolve($value);
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
    /**
     * @inheritdoc
     */
    public function createRejected($reason)
    {
        $promise = \Builderius\React\Promise\reject($reason);
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
    /**
     * @inheritdoc
     */
    public function all(array $promisesOrValues)
    {
        // TODO: rework with generators when PHP minimum required version is changed to 5.5+
        $promisesOrValues = \Builderius\GraphQL\Utils\Utils::map($promisesOrValues, static function ($item) {
            return $item instanceof \Builderius\GraphQL\Executor\Promise\Promise ? $item->adoptedPromise : $item;
        });
        $promise = \Builderius\React\Promise\all($promisesOrValues)->then(static function ($values) use($promisesOrValues) : array {
            $orderedResults = [];
            foreach ($promisesOrValues as $key => $value) {
                $orderedResults[$key] = $values[$key];
            }
            return $orderedResults;
        });
        return new \Builderius\GraphQL\Executor\Promise\Promise($promise, $this);
    }
}
