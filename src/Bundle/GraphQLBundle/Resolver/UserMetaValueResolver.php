<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class UserMetaValueResolver extends AbstractLocalVarsAwareResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['User'];
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
        if (!isset($args['key'])) {
            return null;
        }
        $args = $this->processArguments($args, $info->path);

        /** @var \WP_User $objectValue */
        return get_user_meta($objectValue->ID, $args['key'], true);
    }
}