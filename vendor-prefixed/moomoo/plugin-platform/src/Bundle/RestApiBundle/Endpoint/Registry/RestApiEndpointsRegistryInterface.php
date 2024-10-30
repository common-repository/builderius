<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\Registry;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;
interface RestApiEndpointsRegistryInterface
{
    /**
     * @return RestApiEndpointInterface[]
     */
    public function getEndpoints();
}
