<?php


namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class PostCommentsOpenResolver implements GraphQLFieldResolverInterface
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
        return 'comments_open';
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
        /** @var \WP_Post $objectValue */
        return comments_open($objectValue);
    }
}