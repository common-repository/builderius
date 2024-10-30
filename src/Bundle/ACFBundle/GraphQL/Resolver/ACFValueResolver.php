<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFValueResolver extends AbstractACFValueResolver
{
    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (!isset($args['name'])) {
            return null;
        }
        if (!$this->isOption && !$objectValue->ID && !$objectValue->term_id && !$objectValue->comment_ID) {
            return null;
        }
        if ($this->localVarsHelper) {
            $args = $this->localVarsHelper->processArguments($args, $info->path);
        }

        return get_field($args['name'], $this->getOwnerId($objectValue));
    }
}