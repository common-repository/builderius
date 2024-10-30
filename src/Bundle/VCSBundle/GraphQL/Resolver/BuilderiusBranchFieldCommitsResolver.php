<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommitInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusBranchFieldCommitsResolver implements GraphQLFieldResolverInterface
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
        return BuilderiusBranch::COMMITS_FIELD;
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
        if ($objectValue instanceof BuilderiusBranchInterface) {
            $commits = $objectValue->getCommits();
            if (isset($args['newer_than'])) {
                $commitToCompare = $objectValue->getCommit($args['newer_than']);
                if ($commitToCompare instanceof BuilderiusCommitInterface) {
                    $newerCommits = [];
                    $commitToCompareId = $commitToCompare->getId();
                    foreach ($commits as $commit) {
                        if ($commit->getId() > $commitToCompareId) {
                            $newerCommits[] = $commit;
                        }
                    }

                    return $newerCommits;
                }
            }

            return $commits;
        }

        return null;
    }
}