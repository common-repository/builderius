<?php

namespace Builderius\Bundle\GraphQLBundle\RestApi\Endpoint;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;
use Builderius\Symfony\Component\Cache\CacheItem;

class BuilderiusGraphQLDataVarsEndpoint implements RestApiEndpointInterface
{
    const NAMESPACE = 'wp/v2';
    const REST_BASE = 'builderius-graphql-data-vars';

    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     */
    public function __construct(
        BuilderiusPersistentObjectCache $persistentCache
    )
    {
        $this->persistentCache = $persistentCache;
    }

    /**
     * @inheritDoc
     */
    public function registerRoutes()
    {
        register_rest_route(self::NAMESPACE, '/' . self::REST_BASE, [
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [$this, 'getGraphQLDataVarsValues'],
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
    public function getGraphQLDataVarsValues(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        if (!isset($data['templateType']) || !isset($data['queries']) || !isset($data['rootDataParams'])) {
            return rest_ensure_response([]);
        }
        $rootDataParams = $data['rootDataParams'];
        $url = $rootDataParams['url'];
        $params = [];
        if (isset($rootDataParams['params']) && isset($rootDataParams['params']['GET']) && is_array($rootDataParams['params']['GET'])) {
            $params = [];
            if (isset($rootDataParams['params']['GET'][0])) {
                foreach ($rootDataParams['params']['GET'] as $value) {
                    $params[$value['key']] = isset($value['value']) ? $value['value'] : 0;
                }
            } else {
                $params = $rootDataParams['params']['GET'];
            }
        }
        $queryString = http_build_query($params);
        if (!empty($queryString)) {
            if (strpos($url, '?') === false) {
                $url = sprintf('%s?%s', $url, $queryString);
            } else {
                $url = sprintf('%s&%s', $url, $queryString);
            }
        }
        /** @var CacheItem $tmpltTypeCacheItem */
        $tmpltTypeCacheItem = $this->persistentCache->getItem('applicant_graphql_template_type');
        $tmpltTypeCacheItem->set($data['templateType']);
        $this->persistentCache->save($tmpltTypeCacheItem);

        /** @var CacheItem $tmpltIdCacheItem */
        $tmpltIdCacheItem = $this->persistentCache->getItem('applicant_graphql_template_id');
        $tmpltIdCacheItem->set($data['templateId']);
        $this->persistentCache->save($tmpltIdCacheItem);

        /** @var CacheItem $queriesCacheItem */
        $queriesCacheItem = $this->persistentCache->getItem('applicant_graphql_queries');
        $queriesCacheItem->set($data['queries']);
        $this->persistentCache->save($queriesCacheItem);

        $wpCurl = new \WP_Http_Curl();

        $args = [
            'method' => 'POST',
            'body' => [
                'builderius-applicant-graphql-datavars' => true
            ],
            'timeout' => -1,
            'connection_timeout' => -1,
            'cookies' => $_COOKIE,
            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
            'stream' => false,
            'filename' => false,
            'decompress' => false,
            'verify' => false,
            'verifyName' => false,
            'sslverify' => false
        ];
        $authHeader = $request->get_header('Authorization');
        if (null !== $authHeader) {
            $args['headers'] = ['Authorization' => $authHeader];
        }
        $response = $wpCurl->request(
            $url,
            $args
        );
        if ($response instanceof \WP_Error) {
            return rest_ensure_response(
                $response->get_error_message()
            );
        }

        /** @var CacheItem $dataCacheItem */
        $dataCacheItem = $this->persistentCache->getItem('applicant_graphql_data');
        $data = $dataCacheItem->get();
        $this->persistentCache->delete('applicant_graphql_data');

        return rest_ensure_response(
            is_array($data) ? $data : []
        );
    }
}