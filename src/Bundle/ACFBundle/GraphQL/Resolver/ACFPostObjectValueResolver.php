<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFPostObjectValueResolver extends AbstractACFValueResolver
{
    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (!isset($args['name']) || (!$this->isOption && !$objectValue->ID)) {
            return new \stdClass();
        }
        if ($this->localVarsHelper) {
            $args = $this->localVarsHelper->processArguments($args, $info->path);
        }
        $val = get_field($args['name'], $this->getOwnerId($objectValue));
        if (is_int($val)) {
            $val = get_post($val);
        } elseif(is_array($val) && isset($val['ID'])) {
            $val = get_post($val['ID']);
        }

        return $val instanceof \WP_Post ? $val : new \stdClass();
    }
}