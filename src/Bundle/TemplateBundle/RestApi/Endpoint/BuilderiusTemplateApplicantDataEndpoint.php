<?php

namespace Builderius\Bundle\TemplateBundle\RestApi\Endpoint;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;
use Builderius\Symfony\Component\Cache\CacheItem;

class BuilderiusTemplateApplicantDataEndpoint implements RestApiEndpointInterface
{
    const NAMESPACE = 'wp/v2';
    const REST_BASE = 'builderius-template-applicant-data';

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
                'callback' => [$this, 'getApplicantData'],
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
        return true;
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function getApplicantData(\WP_REST_Request $request, $url = null)
    {
        $data = $request->get_json_params();
        if (!isset($data['url'])) {
            return rest_ensure_response([]);
        }
        $url = $url !== null ? $url : $data['url'];
        $queryString = '';
        if (isset($data['params']) && isset($data['params']['GET']) && is_array($data['params']['GET'])) {
            $params = [];
            if (isset($data['params']['GET'][0])) {
                foreach ($data['params']['GET'] as $value) {
                    $params[$value['key']] = isset($value['value']) ? $value['value'] : 0;
                }
            } else {
                $params = $data['params']['GET'];
            }
            $queryString = http_build_query($params);
        }
        if (!empty($queryString)) {
            if (strpos($url, '?') === false) {
                $url = sprintf('%s?%s', $url, $queryString);
            } else {
                $url = sprintf('%s&%s', $url, $queryString);
            }
        }
        $uniqid = uniqid();
        $wpCurl = new \WP_Http_Curl();
        $args = [
            'method' => 'POST',
            'body' => [
                'builderius-applicant-data' => true,
                'disable_theme' => (isset($data['type']) && isset($data['sub_type']) && $data['type'] === 'template' && $data['sub_type'] === 'hook') ? "false" : "true",
                'uniqid' => $uniqid
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
        $key = sprintf('applicant_data_%s', $uniqid);
        /** @var CacheItem $dataCacheItem */
        $dataCacheItem = $this->persistentCache->getItem($key);
        $data = $dataCacheItem->get();
        $this->persistentCache->delete($key);

        return rest_ensure_response(
            $data
        );
    }
}