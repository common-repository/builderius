<?php

namespace Builderius\Bundle\ReleaseBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;

class ClearPublishedReleaseCacheEventListener
{
    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @return $this
     */
    public function __construct(BuilderiusPersistentObjectCache $persistentCache)
    {
        $this->persistentCache = $persistentCache;

        return $this;
    }

    public function deleteCache()
    {
        $this->persistentCache->clear('published-');
    }
}