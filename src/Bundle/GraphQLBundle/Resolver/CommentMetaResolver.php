<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class CommentMetaResolver implements GraphQLFieldResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['Comment'];
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
        return 'meta';
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
        /** @var \WP_Comment $objectValue */
        $array = get_comment_meta($objectValue->comment_ID, '', true);
        $values = [];
        foreach ($array as $key => $value) {
            $values[] = ['key' => $key, 'value' => reset($value)];
        }

        return $values;
    }
}