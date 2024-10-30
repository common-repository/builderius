<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFMultiplePostObjectValueResolver extends AbstractACFValueResolver
{
    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (!isset($args['name'])) {
            return null;
        }
        if (!$this->isOption && !$objectValue->ID) {
            return null;
        }
        if ($this->localVarsHelper) {
            $args = $this->localVarsHelper->processArguments($args, $info->path);
        }
        $val = get_field($args['name'], $this->getOwnerId($objectValue));
        if (!is_array($val)) {
            return [];
        }
        foreach ($val as &$item) {
            if(is_int($item)) {
                $item = get_post($item);
            } elseif(is_array($item) && isset($item['ID'])) {
                $item = get_post($item['ID']);
            }
            if (!$item instanceof \WP_Post) {
                return [];
            }
        }

        return $val;
    }
}