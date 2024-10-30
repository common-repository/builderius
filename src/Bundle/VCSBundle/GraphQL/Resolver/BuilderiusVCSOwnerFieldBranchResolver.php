<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Model\AbstractBuilderiusVCSOwner;
use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusVCSOwnerFieldBranchResolver implements GraphQLFieldResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusVCSOwner'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return AbstractBuilderiusVCSOwner::BRANCH_GRAPHQL;
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
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (isset($args['name']) && $objectValue instanceof BuilderiusVCSOwnerInterface) {
            return $objectValue->getBranch($args['name']);
        }

        return null;
    }
}