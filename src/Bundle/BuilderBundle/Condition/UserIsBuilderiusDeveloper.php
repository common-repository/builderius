<?php

namespace Builderius\Bundle\BuilderBundle\Condition;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class UserIsBuilderiusDeveloper extends AbstractCondition
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
    protected function getResult()
    {
        /** @var \WP_User|bool $user */
        $user = $this->cache->get('builderius_current_user');
        if (false === $user) {
            $user = apply_filters('builderius_get_current_user', wp_get_current_user());
        }

        return $user && $user->has_cap('builderius-development');
    }
}