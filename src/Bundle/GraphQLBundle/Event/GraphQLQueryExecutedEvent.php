<?php

namespace Builderius\Bundle\GraphQLBundle\Event;

use Builderius\GraphQL\Executor\ExecutionResult;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class GraphQLQueryExecutedEvent extends Event
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var ExecutionResult
     */
    private $result;

    /**
     * @param string|DocumentNode $query
     * @param ExecutionResult $result
     */
    public function __construct(
        $query, ExecutionResult $result
    )
    {
        $this->query = $query;
        $this->result = $result;
    }

    /**
     * @return string|DocumentNode
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return ExecutionResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ExecutionResult $result
     * @return $this
     */
    public function setResult(ExecutionResult $result)
    {
        $this->result = $result;

        return $this;
    }
}