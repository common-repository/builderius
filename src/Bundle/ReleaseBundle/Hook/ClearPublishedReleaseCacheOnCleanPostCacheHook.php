<?php

namespace Builderius\Bundle\ReleaseBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class ClearPublishedReleaseCacheOnCleanPostCacheHook extends AbstractAction
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
        /** @var \WP_Post $post */
        $post = func_get_arg(1);
        if (strpos($post->post_type, 'builderius') === false) {
            $this->persistentCache->clear('published-');
        }
    }
}