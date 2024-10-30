<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\Registrator;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;
interface RestApiEndpointsRegistratorInterface
{
    /**
     * @param RestApiEndpointInterface[] $endpoints
     */
    public function registerRestEndpoints(array $endpoints);
}
