<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class PluginsHooksRemovalHook extends AbstractAction
{
    /**
     * @var PluginsVersionsProvider
     */
    private $pluginsVersionsProvider;

    /**
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     * @return $this
     */
    public function setPluginsVersionsProvider(PluginsVersionsProvider $pluginsVersionsProvider)
    {
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        global $wp_filter;
        $pluginNames = [];
        foreach ($this->pluginsVersionsProvider->getPluginsVersions() as $name => $version) {
            if (strpos($name, '.php') === false) {
                $pluginNames[] = $name;
            }
        }
        foreach ($wp_filter as $fName => $fHook) {
            foreach ($fHook->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $name => $callback) {
                    try {
                        if (is_array($callback['function'])) {
                            $reflection = new \ReflectionClass((object)$callback['function']);
                        } elseif(is_object($callback['function'])) {
                            $reflection = new \ReflectionClass($callback['function']);
                        } elseif(is_string($callback['function'])) {
                            $reflection = new \ReflectionFunction($callback['function']);
                        }
                        $fileName = $reflection->getFileName();
                        if (strpos($fileName, 'wp-content/plugins') !== false) {
                            foreach ($pluginNames as $pluginName) {
                                if (strpos($fileName, $pluginName) === false) {
                                    remove_filter($fName, $callback['function'], $priority);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }


            /*$cachedHooks = $this->cache->get(sprintf('theme_%s_filters', $fName));
            if (false !== $cachedHooks) {
                foreach ($cachedHooks as $priority => $callbacks) {
                    foreach ($callbacks as $callback) {
                        remove_action($fName, $callback['function'], $priority);
                    }
                }
            }*/
        }
    }
}