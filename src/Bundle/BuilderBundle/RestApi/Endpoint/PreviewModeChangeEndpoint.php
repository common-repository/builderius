<?php

namespace Builderius\Bundle\BuilderBundle\RestApi\Endpoint;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;

class PreviewModeChangeEndpoint implements RestApiEndpointInterface
{
    const NAMESPACE = 'wp/v2';
    const REST_BASE = 'preview-mode-change';

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
    public function permissionCheck()
    {
        $user = apply_filters('builderius_get_current_user', wp_get_current_user());

        return user_can($user, 'builderius-development');
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function process(\WP_REST_Request $request)
    {
        $response = new \WP_REST_Response;
        $params = $request->get_json_params();

        if (!isset($params['preview_mode'])) {
            $response->set_data('no preview_mode provided');
            $response->set_status(404);

            return $response;
        }
        $user = apply_filters('builderius_get_current_user', wp_get_current_user());
        update_user_meta($user->ID, 'builderius_dev_preview', $params['preview_mode'] === 'dev');

        return $response;
    }
}