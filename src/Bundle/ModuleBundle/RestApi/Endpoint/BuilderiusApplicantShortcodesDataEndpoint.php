<?php

namespace Builderius\Bundle\ModuleBundle\RestApi\Endpoint;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Bundle\TemplateBundle\RestApi\Endpoint\BuilderiusTemplateApplicantDataEndpoint;
use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\RestApiEndpointInterface;
use Builderius\Symfony\Component\Cache\CacheItem;

class BuilderiusApplicantShortcodesDataEndpoint implements RestApiEndpointInterface
{
    const NAMESPACE = 'wp/v2';
    const REST_BASE = 'builderius-applicant-shortcodes-data';

    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @var BuilderiusTemplateApplicantDataEndpoint
     */
    private $applicantDataEndpoint;

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @param BuilderiusTemplateApplicantDataEndpoint $applicantDataEndpoint
     */
    public function __construct(
        BuilderiusPersistentObjectCache $persistentCache,
        BuilderiusTemplateApplicantDataEndpoint $applicantDataEndpoint
    ) {
        $this->persistentCache = $persistentCache;
        $this->applicantDataEndpoint = $applicantDataEndpoint;
    }

    /**
     * @inheritDoc
     */
    public function registerRoutes()
    {
        register_rest_route(self::NAMESPACE, '/' . self::REST_BASE, [
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [$this, 'getShortcodeData'],
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
    public function getShortcodeData(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        if (!isset($data['url']) || !isset($data['items'])) {
            return rest_ensure_response([]);
        }
        $url = $data['url'];
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
        $shortcodesData = [];
        $applicantData = $this->applicantDataEndpoint->getApplicantData($request, $url)->get_data();
        foreach ($data['items'] as $shortcode) {
            $uniqid = uniqid();
            /** @var CacheItem $shortcodeCacheItem */
            $shortcodeCacheItem = $this->persistentCache->getItem(sprintf('applicant_shortcode_%s', $uniqid));
            $shortcodeCacheItem->set($shortcode);
            $this->persistentCache->save($shortcodeCacheItem);

            $wpCurl = new \WP_Http_Curl();

            $args = [
                'method' => 'POST',
                'body' => [
                    'builderius-applicant-shortcode-data' => true,
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
                $data = [];
            } else {
                $key = sprintf('applicant_shortcode_data_%s', $uniqid);
                /** @var CacheItem $dataCacheItem */
                $dataCacheItem = $this->persistentCache->getItem($key);
                $data = $dataCacheItem->get();
                $this->persistentCache->delete($key);
            }
            $data['module'] = 'Shortcode';
            $data['value'] = $shortcode;
            if (!empty($data['scripts'])) {
                foreach ($data['scripts'] as $handle => $script) {
                    if (isset($applicantData['scripts']) && in_array($script, $applicantData['scripts'])) {
                        unset($data['scripts'][$handle]);
                    }
                }
            }
            if (!empty($data['styles'])) {
                foreach ($data['styles'] as $handle => $style) {
                    if (isset($applicantData['styles']) && in_array($style, $applicantData['styles'])) {
                        unset($data['styles'][$handle]);
                    }
                }
            }
            if (!empty($data['inline_styles'])) {
                if (isset($data['inline_styles']['header'])) {
                    foreach ($data['inline_styles']['header'] as $k => $style) {
                        if (
                            isset($applicantData['inline_styles']) &&
                            isset($applicantData['inline_styles']['header']) &&
                            in_array($style, $applicantData['inline_styles']['header'])
                        ) {
                            unset($data['inline_styles']['header'][$k]);
                        }
                    }
                    if (empty($data['inline_styles']['header'])) {
                        unset($data['inline_styles']['header']);
                    } else {
                        $data['inline_styles']['header'] = array_values($data['inline_styles']['header']);
                    }
                }
                if (isset($data['inline_styles']['footer'])) {
                    foreach ($data['inline_styles']['footer'] as $k => $style) {
                        if (
                            isset($applicantData['inline_styles']) &&
                            isset($applicantData['inline_styles']['footer']) &&
                            in_array($style, $applicantData['inline_styles']['footer'])
                        ) {
                            unset($data['inline_styles']['footer'][$k]);
                        }
                    }
                    if (empty($data['inline_styles']['footer'])) {
                        unset($data['inline_styles']['footer']);
                    } else {
                        $data['inline_styles']['footer'] = array_values($data['inline_styles']['footer']);
                    }
                }
            }
            if (!empty($data['inline_scripts'])) {
                if (isset($data['inline_scripts']['header'])) {
                    foreach ($data['inline_scripts']['header'] as $k => $script) {
                        if (
                            isset($applicantData['inline_scripts']) &&
                            isset($applicantData['inline_scripts']['header']) &&
                            in_array($script, $applicantData['inline_scripts']['header'])
                        ) {
                            unset($data['inline_scripts']['header'][$k]);
                        }
                    }
                    if (empty($data['inline_scripts']['header'])) {
                        unset($data['inline_scripts']['header']);
                    } else {
                        $data['inline_scripts']['header'] = array_values($data['inline_scripts']['header']);
                    }
                }
                if (isset($data['inline_scripts']['footer'])) {
                    foreach ($data['inline_scripts']['footer'] as $k => $script) {
                        if (
                            isset($applicantData['inline_scripts']) &&
                            isset($applicantData['inline_scripts']['footer']) &&
                            in_array($script, $applicantData['inline_scripts']['footer'])
                        ) {
                            unset($data['inline_scripts']['footer'][$k]);
                        }
                    }
                    if (empty($data['inline_scripts']['footer'])) {
                        unset($data['inline_scripts']['footer']);
                    } else {
                        $data['inline_scripts']['footer'] = array_values($data['inline_scripts']['footer']);
                    }
                }
            }
            $shortcodesData[] = $data;
        }
        return rest_ensure_response(
            $shortcodesData
        );
    }
}