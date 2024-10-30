<?php

namespace Builderius\Bundle\GraphQLBundle\Executor;

use Builderius\GraphQL\Executor\ExecutionResult;

interface BuilderiusEntitiesGraphQLQueriesExecutorInterface
{
    /**
     * @param array $queries
     * @return ExecutionResult[]
     */
    public function execute(array $queries);
}