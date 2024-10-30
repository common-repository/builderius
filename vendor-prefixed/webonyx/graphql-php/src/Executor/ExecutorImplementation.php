<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Executor;

use Builderius\GraphQL\Executor\Promise\Promise;
interface ExecutorImplementation
{
    /**
     * Returns promise of {@link ExecutionResult}. Promise should always resolve, never reject.
     */
    public function doExecute() : \Builderius\GraphQL\Executor\Promise\Promise;
}
