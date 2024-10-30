<?php

namespace Builderius\Bundle\GraphQLBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class GraphQLSubfieldsResolvedEvent extends Event
{
    /**
     * @var array
     */
    private $results;

    /**
     * @var \ArrayObject
     */
    private $fieldNodes;

    /**
     * @param array $results
     * @param \ArrayObject $fieldNodes
     */
    public function __construct(
        array $results, \ArrayObject $fieldNodes
    )
    {
        $this->results = $results;
        $this->fieldNodes = $fieldNodes;
    }

    /**
     * @return \ArrayObject
     */
    public function getFieldNodes()
    {
        return $this->fieldNodes;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param array $results
     * @return $this
     */
    public function setResults(array $results)
    {
        $this->results = $results;

        return $this;
    }
}