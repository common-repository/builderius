<?php

namespace Builderius\MooMoo\Platform\Bundle\QueryBundle\Hooks;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
class PostParentJoinHook extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter
{
    const PARENT_TABLE = 'parent_post';
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $join = \func_get_arg(0);
        $query = \func_get_arg(1);
        if (isset($query->query[\Builderius\MooMoo\Platform\Bundle\QueryBundle\Hooks\ParentNameInHook::QUERY_ARGUMENT]) || isset($query->query[\Builderius\MooMoo\Platform\Bundle\QueryBundle\Hooks\ParentNameNotInHook::QUERY_ARGUMENT])) {
            global $wpdb;
            $join = \sprintf("%s JOIN %s AS %s ON (%s.post_parent = %s.ID) ", $wpdb->posts, self::PARENT_TABLE, $wpdb->posts, self::PARENT_TABLE, $join);
        }
        return $join;
    }
}
