<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteNotCommittedConfigResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    protected $branchFactory;

    /**
     * @var \WP_Query
     */
    protected $wpQuery;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusBranchFromPostFactory $branchFactory
     * @param \WP_Query $wpQuery
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusBranchFromPostFactory $branchFactory,
        \WP_Query $wpQuery,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->branchFactory = $branchFactory;
        $this->wpQuery = $wpQuery;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootMutation'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'deleteNotCommittedConfig';
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
        $branchId = $args['branch_id'];
        $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        $branchPost = get_post((int)$branchId);
        if (empty($branchPost) || empty($branchPost->ID) ||
            BuilderiusBranchPostType::POST_TYPE !== $branchPost->post_type) {
            throw new \Exception('Invalid Branch ID.', 400);
        }
        $currUserHeadCommitsPosts = $this->wpQuery->query([
            'post_type' => BuilderiusBranchHeadCommitPostType::POST_TYPE,
            'post_parent' => $branchId,
            'name' => sprintf('branch_%d_user_%d', $branchId, $currUserId),
            'author' => $currUserId,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'orderby' => 'ID',
            'order' => 'DESC'
        ]);
        if (!empty($currUserHeadCommitsPosts)) {
            $branchHeadCommitPost = reset($currUserHeadCommitsPosts);
            $deletedPost = wp_delete_post($branchHeadCommitPost->ID, true);
            if ($deletedPost instanceof \WP_Error) {
                throw new \Exception($deletedPost->get_error_message(), 400);
            }
            $this->cache->delete(sprintf('builderius_branch_%d_head_commit_post_user_%d', $branchPost->ID, $currUserId));
            $this->eventDispatcher->dispatch(new PostContainingEvent($branchPost), 'builderius_branch_not_committed_config_deleted');
        }
        $branch = $this->branchFactory->createBranch($branchPost);

        return new \ArrayObject(['branch' => $branch]);
    }
}