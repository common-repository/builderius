<?php

namespace Builderius\Bundle\BuilderBundle\Condition;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class BuilderiusPreviewInDevMode extends AbstractCondition
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
     * {@inheritDoc}
     */
    protected function getResult()
    {
        try {
            /** @var \WP_User|bool $user */
            $user = $this->cache->get('builderius_current_user');
            if (false === $user) {
                $user = apply_filters('builderius_get_current_user', wp_get_current_user());
            }
            $devMode = get_user_meta($user->ID, 'builderius_dev_preview', false);
            $devMode = empty($devMode) ? true : (bool)$devMode[0];

            return (bool)$devMode === true && user_can($user, 'builderius-development');
        } catch (\Exception $e) {
            return false;
        }
    }
}