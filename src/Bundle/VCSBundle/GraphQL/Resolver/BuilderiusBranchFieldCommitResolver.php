<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusBranchFieldCommitResolver implements GraphQLFieldResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusBranch'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'commit';
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
        if (isset($args['name']) && $objectValue instanceof BuilderiusBranchInterface) {
            return $objectValue->getCommit($args['name']);
        }

        return null;
    }
}