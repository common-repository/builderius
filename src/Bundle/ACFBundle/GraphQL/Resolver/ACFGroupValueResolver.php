<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFGroupValueResolver extends AbstractACFValueResolver
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

        return $val;
    }
}