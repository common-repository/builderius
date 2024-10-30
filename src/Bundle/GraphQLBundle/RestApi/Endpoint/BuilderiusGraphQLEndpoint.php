<?php

namespace Builderius\Bundle\GraphQLBundle\RestApi\Endpoint;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;

class BuilderiusGraphQLEndpoint implements RestApiEndpointInterface
{
    const NAMESPACE = 'wp/v2';
    const REST_BASE = 'builderius';

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     */
    public function __construct(BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor)
    {
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
    }


    /**
     * @inheritDoc
     */
    public function registerRoutes()
    {
        register_rest_route(self::NAMESPACE, '/' . self::REST_BASE, [
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [$this, 'process'],
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
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function process(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        if (!isset($data['queries'])) {
            return rest_ensure_response([]);
        }
        $results = $this->graphQLQueriesExecutor->execute($data['queries']);

        return rest_ensure_response($results);
    }
}