<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\AssetRemovalEvent;
use Builderius\Bundle\TemplateBundle\Hook\RemoveUnnecessaryScriptsAndStylesHook;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class ThemeScriptsAndStylesRemovalHook extends AbstractAction
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

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
     * @param EventDispatcher $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        if (isset($_POST['builderius-applicant-data']) && isset($_POST['disable_theme']) && $_POST['disable_theme'] === "false") {
            return;
        }
        $themeName = get_option('stylesheet');
        global $wp_scripts;
        /** @var \_WP_Dependency $script */
        foreach ($wp_scripts->queue as $script) {
            if (isset($wp_scripts->registered[$script]) &&
                (
                    strpos($wp_scripts->registered[$script]->src, 'wp-content/themes') !== false ||
                    strpos($wp_scripts->registered[$script]->src, 'wp-content/plugins/oxygen') !== false ||
                    strpos($script, $themeName) !== false
                )
            ) {
                $event = new AssetRemovalEvent('script', $script->handle, true);
                $this->eventDispatcher->dispatch($event, 'asset_removal');
                if (true === $event->getResult()) {
                    wp_deregister_script($script);
                }
            }
        }

        global $wp_styles;
        /** @var \_WP_Dependency $style */
        foreach ($wp_styles->queue as $style) {
            if (isset($wp_styles->registered[$style]) &&
                (
                    strpos($wp_styles->registered[$style]->src, 'wp-content/themes') !== false ||
                    strpos($wp_styles->registered[$style]->src, 'wp-content/plugins/oxygen') !== false ||
                    strpos($style, $themeName) !== false
                )
            ) {
                $event = new AssetRemovalEvent('style', $style->handle, true);
                $this->eventDispatcher->dispatch($event, 'asset_removal');
                if (true === $event->getResult()) {
                    wp_dequeue_style($style);
                }
            }
        }
        $themeFooterFilters = $this->cache->get('theme_wp_head_filters');
        if (is_array($themeFooterFilters)) {
            foreach ($themeFooterFilters as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    remove_action('wp_head', $callback['function'], $priority);
                }
            }
        }
        $themeFooterFilters = $this->cache->get('theme_wp_footer_filters');
        if (is_array($themeFooterFilters)) {
            foreach ($themeFooterFilters as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    remove_action('wp_footer', $callback['function'], $priority);
                }
            }
        }
    }
}