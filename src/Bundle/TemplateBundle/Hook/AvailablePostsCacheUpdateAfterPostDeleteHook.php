<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\AvailablePagesProvider;
use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\AvailablePostsProvider;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class AvailablePostsCacheUpdateAfterPostDeleteHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $post = func_get_arg(1);
        if ($post instanceof \WP_Post) {
            if ($post->post_type === 'page') {
                $cache = wp_cache_get(AvailablePagesProvider::CACHE_KEY);
                if (false !== $cache && is_array($cache)) {
                    unset($cache[$post->ID]);
                    wp_cache_set(AvailablePagesProvider::CACHE_KEY, $cache);
                }
            } else {
                $cache = wp_cache_get(AvailablePostsProvider::CACHE_KEY);
                if (false !== $cache && is_array($cache) && is_array($cache[$post->post_type])) {
                    if (is_array($cache[$post->post_type]['objects'])) {
                        unset($cache[$post->post_type]['objects'][$post->ID]);
                    }
                    foreach ($cache[$post->post_type] as $field => $fieldCache) {
                        if ($field !== 'objects') {
                            unset($cache[$post->post_type][$field]['not_sorted'][$post->ID]);
                            $cache[$post->post_type][$field]['sorted'] =
                                $this->sort($cache[$post->post_type][$field]['not_sorted']);
                        }
                    }
                    wp_cache_set(AvailablePostsProvider::CACHE_KEY, $cache);
                }
            }
        }
    }

    /**
     * @param array $results
     */
    private function sort (array $results)
    {
        usort ($results, function ($a, $b) {
            if ($a['value'] < $b['value']) {
                return -1;
            } elseif ($a['value'] > $b['value']) {
                return 1;
            }

            return 0;
        });

        return $results;
    }
}