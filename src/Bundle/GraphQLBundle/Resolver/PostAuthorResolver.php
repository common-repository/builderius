<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class PostAuthorResolver implements GraphQLFieldResolverInterface
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
        return 'post_author';
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
        $user = get_user_by('ID', $objectValue->post_author);
        if ($user instanceof \WP_User) {
            return $user;
        }

        return new \WP_User();
    }
}