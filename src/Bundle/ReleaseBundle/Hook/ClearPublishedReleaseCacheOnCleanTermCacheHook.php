<?php

namespace Builderius\Bundle\ReleaseBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class ClearPublishedReleaseCacheOnCleanTermCacheHook extends AbstractAction
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
        $this->persistentCache->clear('published-');
    }
}