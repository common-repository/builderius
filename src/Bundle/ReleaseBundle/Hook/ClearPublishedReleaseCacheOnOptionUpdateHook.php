<?php

namespace Builderius\Bundle\ReleaseBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class ClearPublishedReleaseCacheOnOptionUpdateHook extends AbstractAction
{
    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @return $this
     */
    public function setPersistentCache(BuilderiusPersistentObjectCache $persistentCache)
    {
        $this->persistentCache = $persistentCache;

        return $this;
    }

    public function getFunction()
    {
        $optionName = func_get_arg(0);
        if ($optionName !== 'cron' && strpos($optionName, '_transient') === false && strpos($optionName, '_site_transient') === false) {
            $this->persistentCache->clear('published-');
        }
    }
}