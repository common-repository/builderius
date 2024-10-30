<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\ApplicantStylesDataPreProvidingHook;

class StylesBuilderiusTemplateApplicantDataProvider implements BuilderiusTemplateApplicantDataProviderInterface
{
    const EXCLUDED_HANDLES = [
        'wp-block-library-theme',
        'dashicons'
    ];

    /**
     * @var array
     */
    private $styles = [];

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'styles';
    }

    /**
     * @inheritDoc
     */
    public function getData(array $applicantQueryVars = [])
    {
        global $wp_styles;

        $registeredStyles = $wp_styles->registered;
        foreach ($wp_styles->queue as $handle) {
            $this->processHandle($handle, $registeredStyles);
        }
        $cachedCustomWpStylesSets = $this->cache->get(ApplicantStylesDataPreProvidingHook::CACHE_KEY);
        if (is_array($cachedCustomWpStylesSets)) {
            foreach ($cachedCustomWpStylesSets as $cachedCustomWpStyles) {
                $registeredCustomWpStyles = $cachedCustomWpStyles->registered;
                foreach ($cachedCustomWpStyles->queue as $handle) {
                    $this->processHandle($handle, $registeredCustomWpStyles);
                }
            }
        }

        return $this->styles;
    }

    /**
     * @param $handle
     * @param array $registeredStyles
     */
    private function processHandle($handle, array $registeredStyles)
    {
        if (isset($registeredStyles[$handle])) {
            /** @var \_WP_Dependency $wpDependency */
            $wpDependency = $registeredStyles[$handle];
            if (!empty($wpDependency->deps)) {
                foreach ($wpDependency->deps as $depHandle) {
                    $this->processHandle($depHandle, $registeredStyles);
                }
            }
            if (
                !isset($this->styles[$handle]) &&
                !in_array($handle, self::EXCLUDED_HANDLES) &&
                strpos($handle, 'builderius') === false &&
                (((!isset($_POST['disable_theme']) || $_POST['disable_theme'] === "true") && strpos($wpDependency->src, 'wp-content/themes') === false) || $_POST['disable_theme'] === "false") &&
                (($wpDependency->src !== null && $wpDependency->src !== false) || !empty($wpDependency->extra))
            ) {
                $siteUrl = get_site_url();
                $src = $wpDependency->src;
                if (strpos($src, 'wp-content') !== false) {
                    $src = sprintf('%s/wp-content%s', $siteUrl, explode('wp-content', $src)[1]);
                } elseif (strpos($src, 'wp-includes') !== false) {
                    $src = sprintf('%s/wp-includes%s', $siteUrl, explode('wp-includes', $src)[1]);
                } elseif (strpos($src, 'wp-admin') !== false) {
                    $src = sprintf('%s/wp-admin%s', $siteUrl, explode('wp-admin', $src)[1]);
                }
                $extra = $wpDependency->extra;
                unset($extra['path']);
                if (isset($extra['after'])) {
                    $extra['after'] = array_values(array_unique($extra['after']));
                }
                $this->styles[$handle] = [
                    'handle' => $handle,
                    'src' => $src,
                    'ver' => $wpDependency->ver,
                    'extra' => $extra
                ];
            }
        }
    }
}