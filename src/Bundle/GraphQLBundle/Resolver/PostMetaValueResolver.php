<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class PostMetaValueResolver extends AbstractLocalVarsAwareResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['Post'];
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'meta_value';
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (!isset($args['key']) || !property_exists($objectValue, 'ID')) {
            return null;
        }
        $args = $this->processArguments($args, $info->path);

        /** @var \WP_Post $objectValue */
        return get_post_meta($objectValue->ID, $args['key'], true);
    }
}