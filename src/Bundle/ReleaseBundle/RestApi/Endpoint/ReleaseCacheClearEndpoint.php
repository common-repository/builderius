<?php

namespace Builderius\Bundle\ReleaseBundle\RestApi\Endpoint;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;

class ReleaseCacheClearEndpoint implements RestApiEndpointInterface
{
    const NAMESPACE = 'wp/v2';
    const REST_BASE = 'builderius-published-cache-clear';


    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     */
    public function __construct(
        BuilderiusPersistentObjectCache $persistentCache
    ) {
        $this->persistentCache = $persistentCache;
    }

    /**
     * {@inheritDoc}
     */
    public function registerRoutes()
    {
        register_rest_route(self::NAMESPACE, '/' . self::REST_BASE, [
            [
                'methods' => \WP_REST_Server::DELETABLE,
                'callback' => [$this, 'clearCache'],
                'permission_callback' => [$this, 'permissionCheck'],
                'args' => [],
            ],
            'schema' => [],
        ]);
    }

    /**
     * @return bool
     */
    public function permissionCheck() {
        $user = apply_filters('builderius_get_current_user', wp_get_current_user());

        return user_can($user, 'builderius-development');
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function clearCache()
    {
        $this->persistentCache->clear('published-');

        return rest_ensure_response([
            'message' => 'Published cache was cleared successfully.'
        ]);
    }
}