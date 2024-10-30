<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint;

interface RestApiEndpointInterface
{
    /**
     * @see register_rest_route()
     */
    public function registerRoutes();
}
