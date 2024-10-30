<?php

namespace Builderius\MooMoo\Platform\Bundle\QueryBundle\Hooks;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
class PostStatusNotInHook extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter
{
    const QUERY_ARGUMENT = 'post_status__not_in';
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $where = \func_get_arg(0);
        $query = \func_get_arg(1);
        if (isset($query->query[self::QUERY_ARGUMENT]) && \is_array($query->query[self::QUERY_ARGUMENT])) {
            global $wpdb;
            $statuses = $query->query[self::QUERY_ARGUMENT];
            if (!empty($statuses)) {
                $where = \sprintf("%s AND %s.post_status NOT IN ('%s')", $where, $wpdb->posts, \implode("','", $statuses));
            }
        }
        return $where;
    }
}
