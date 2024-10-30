<?php

namespace Builderius\Bundle\GraphQLBundle\Helper;

class GraphQLHelper
{
    public static function array_map_deep($array, $callback, $context) {
        $new = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $new[$key] = self::array_map_deep($val, $callback, $context);
            } else {
                $new[$key] = call_user_func_array($callback, [$val, $context]);
            }
        }
        return $new;
    }
}