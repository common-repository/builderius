<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class AfterSetupThemeHook extends AbstractFilter
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     * @return $this
     */
    public function setCache(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        global $wp_filter;
        foreach ($wp_filter as $fName => $fHook) {
            $this->processHook($fName, sprintf('%s_before', $fName));
        }
        //$this->processHook('wp_head', 'wp_head_before');
        //$this->processHook('wp_footer', 'wp_footer_before');
    }


    /**
     * @inheritDoc
     */
    private function processHook($hookName, $cacheKey)
    {
        global $wp_filter;

        $before = $this->cache->get($cacheKey);
        $after = $wp_filter[$hookName];
        $diff = [];
        foreach ($after->callbacks as $priority => $callbacks) {
            if (isset($before->callbacks[$priority])) {
                foreach ($callbacks as $name => $callback) {
                    if (!isset($before->callbacks[$priority][$name])) {
                        $diff[$priority][$name] = $callback;
                    }
                }

            } else {
                $diff[$priority] = $callbacks;
            }
        }
        $this->cache->delete($cacheKey);
        if (!empty($diff)) {
            $this->cache->set(sprintf('theme_%s_filters', $hookName), $diff);
        }
    }
}