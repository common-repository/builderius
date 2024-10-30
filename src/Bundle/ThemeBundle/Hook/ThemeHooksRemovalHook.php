<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class ThemeHooksRemovalHook extends AbstractAction
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
        if (isset($_POST['builderius-applicant-data']) && isset($_POST['disable_theme']) && $_POST['disable_theme'] === "false") {
            return;
        }
        global $wp_filter;
        foreach ($wp_filter as $fName => $fHook) {
            $cachedHooks = $this->cache->get(sprintf('theme_%s_filters', $fName));
            if (false !== $cachedHooks) {
                foreach ($cachedHooks as $priority => $callbacks) {
                    foreach ($callbacks as $callback) {
                        remove_action($fName, $callback['function'], $priority);
                    }
                }
            }
        }
    }
}