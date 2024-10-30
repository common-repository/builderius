<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class BeforeSetupThemeHook extends AbstractFilter
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
            $this->cache->set(sprintf('%s_before', $fName), $fHook);
        }
    }
}