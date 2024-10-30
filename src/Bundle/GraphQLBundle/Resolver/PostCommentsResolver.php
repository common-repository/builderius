<?php


namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class PostCommentsResolver implements GraphQLFieldResolverInterface
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
        return 'comments';
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
        return get_comments(['post_id' => $objectValue->ID]);
    }
}