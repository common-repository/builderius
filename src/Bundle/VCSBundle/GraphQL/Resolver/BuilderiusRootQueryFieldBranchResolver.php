<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldBranchResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusBranchFromPostFactory $branchFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        BuilderiusBranchFromPostFactory $branchFromPostFactory,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->branchFromPostFactory = $branchFromPostFactory;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootQuery'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'branch';
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
        $id = $args['id'];
        $branch = $this->cache->get(sprintf('builderius_branch_%s', $id));
        if (false === $branch) {
            $branchPost = get_post($id);
            if (!$branchPost || $branchPost->post_type !== BuilderiusBranchPostType::POST_TYPE) {
                throw new \Exception('Invalid Branch ID.', 400);
            }
            $this->cache->set(sprintf('builderius_branch_post_%s', $id), $branchPost);
            $branch = $this->branchFromPostFactory->createBranch($branchPost);
            $this->cache->set(sprintf('builderius_branch_%s', $branchPost->ID), $branch);
        }

        return $branch;
    }
}