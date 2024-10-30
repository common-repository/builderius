<?php

namespace Builderius\Bundle\GraphQLBundle\Event;

use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class GraphQLQueryBeforeExecutionEvent extends Event
{
    /**
     * @var string
     */
    private $query;

    /**
     * @param string $query
     */
    public function __construct(
        string $query
    )
    {
        $this->query = $query;
    }

    /**
     * @return string|DocumentNode
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string|DocumentNode
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }
}