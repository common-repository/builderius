<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldBranchesResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusBranchFromPostFactory $branchFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusBranchFromPostFactory $branchFromPostFactory,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->wpQuery = $wpQuery;
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
        return 'branches';
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
        if (isset($args['owner_id'])) {
            $ownerId = $args['owner_id'];
            $branches = $this->cache->get(sprintf('builderius_template_%s_branches', $ownerId));
            if (false === $branches) {
                $branches = [];
                $branchesPosts = $this->cache->get(sprintf('builderius_template_%s_branches_posts', $ownerId));
                if (false === $branchesPosts) {
                    $branchesPosts = $this->wpQuery->query([
                        'post_type' => BuilderiusBranchPostType::POST_TYPE,
                        'post_parent' => $ownerId,
                        'post_status' => get_post_stati(),
                        'posts_per_page' => -1,
                        'no_found_rows' => true,
                        'orderby' => 'ID',
                        'order' => 'DESC'
                    ]);
                    $this->cache->set(sprintf('builderius_template_%s_branches_posts', $ownerId), $branchesPosts);
                }
                /** @var BuilderiusBranchInterface[] $branches */
                foreach ($branchesPosts as $branchPost) {
                    if (false === $this->cache->get(sprintf('builderius_branch_post_%s', $branchPost->ID))) {
                        $this->cache->set(sprintf('builderius_branch_post_%s', $branchPost->ID), $branchPost);
                    }
                    $branch = $this->branchFromPostFactory->createBranch($branchPost);
                    $this->cache->set(sprintf('builderius_branch_%s', $branchPost->ID), $branch);
                    $branches[] = $branch;
                }
                $this->cache->set(sprintf('builderius_template_%s_branches', $ownerId), $branches);
            }

            return $branches;
        } else {
            $branches = $this->cache->get('builderius_branches');
            if (false === $branches) {
                $branches = [];
                $branchesPosts = $this->cache->get('builderius_branches_posts');
                if (false === $branchesPosts) {
                    $branchesPosts = $this->wpQuery->query([
                        'post_type' => BuilderiusBranchPostType::POST_TYPE,
                        'post_status' => get_post_stati(),
                        'posts_per_page' => -1,
                        'no_found_rows' => true,
                        'orderby' => 'ID',
                        'order' => 'DESC'
                    ]);
                    $this->cache->set('builderius_branches_posts', $branchesPosts);
                }
                /** @var BuilderiusBranchInterface[] $branches */
                foreach ($branchesPosts as $branchPost) {
                    if (false === $this->cache->get(sprintf('builderius_branch_post_%s', $branchPost->ID))) {
                        $this->cache->set(sprintf('builderius_branch_post_%s', $branchPost->ID), $branchPost);
                    }
                    $branch = $this->branchFromPostFactory->createBranch($branchPost);
                    $this->cache->set(sprintf('builderius_branch_%s', $branchPost->ID), $branch);
                    $branches[] = $branch;
                }
                $this->cache->set('builderius_branches', $branches);
            }

            return $branches;
        }
    }
}