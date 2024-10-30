<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\ApplicantScriptsDataPreProvidingHook;

class ScriptsBuilderiusTemplateApplicantDataProvider implements BuilderiusTemplateApplicantDataProviderInterface
{
    const EXCLUDED_HANDLES = [
        'wp-embed',
        'wp-emoji-release'
    ];

    /**
     * @var array
     */
    private $scripts = [];

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
        return 'scripts';
    }

    /**
     * @inheritDoc
     */
    public function getData(array $applicantQueryVars = [])
    {
        global $wp_scripts;

        $registeredScripts = $wp_scripts->registered;
        foreach ($wp_scripts->queue as $handle) {
            $this->processHandle($handle, $registeredScripts);
        }
        $cachedCustomWpScriptsSets = $this->cache->get(ApplicantScriptsDataPreProvidingHook::CACHE_KEY);
        if (is_array($cachedCustomWpScriptsSets)) {
            foreach ($cachedCustomWpScriptsSets as $cachedCustomWpScripts) {
                $registeredCustomWpScripts = $cachedCustomWpScripts->registered;
                foreach ($cachedCustomWpScripts->queue as $handle) {
                    $this->processHandle($handle, $registeredCustomWpScripts);
                }
            }
        }

        return $this->scripts;
    }

    /**
     * @param $handle
     * @param array $registeredScripts
     */
    private function processHandle($handle, array $registeredScripts)
    {
        if (isset($registeredScripts[$handle])) {
            /** @var \_WP_Dependency $wpDependency */
            $wpDependency = $registeredScripts[$handle];
            if (!empty($wpDependency->deps)) {
                foreach ($wpDependency->deps as $depHandle) {
                    $this->processHandle($depHandle, $registeredScripts);
                }
            }
            if (
                !isset($this->scripts[$handle]) &&
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
                $this->scripts[$handle] = [
                    'handle' => $handle,
                    'src' => $src,
                    'ver' => $wpDependency->ver,
                    'extra' => $wpDependency->extra
                ];
            }
        }
    }
}