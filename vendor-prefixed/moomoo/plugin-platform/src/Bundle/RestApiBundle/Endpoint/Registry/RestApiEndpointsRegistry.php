<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\Registry;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;
class RestApiEndpointsRegistry implements \Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\Registry\RestApiEndpointsRegistryInterface
{
    private $endpoints = [];
    /**
     * @param RestApiEndpointInterface $endpoint
     */
    public function addEndpoint(\Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface $endpoint)
    {
        $this->endpoints[] = $endpoint;
    }
    /**
     * @inheritDoc
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }
}
