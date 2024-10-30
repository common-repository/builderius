<?php

declare (strict_types=1);
namespace Builderius\GraphQL;

use Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise;
class Deferred extends \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromise
{
    /**
     * @param callable() : mixed $executor
     */
    public static function create(callable $executor) : self
    {
        return new self($executor);
    }
    /**
     * @param callable() : mixed $executor
     */
    public function __construct(callable $executor)
    {
        parent::__construct($executor);
    }
}
