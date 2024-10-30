<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractBuilderiusRootMutationFieldCUBranchResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface
     */
    protected $configChecker;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    protected $branchFactory;

    /**
     * @var \WP_Query
     */
    protected $wpQuery;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateContentConfigCheckerInterface $configChecker
     * @param BuilderiusBranchFromPostFactory $branchFactory
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateContentConfigCheckerInterface $configChecker,
        BuilderiusBranchFromPostFactory $branchFactory,
        \WP_Query $wpQuery
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configChecker = $configChecker;
        $this->branchFactory = $branchFactory;
        $this->wpQuery = $wpQuery;
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
     * @param array $args
     * @return \stdClass
     * @throws \Exception
     */
    protected function getPreparedPost(array $args)
    {
        $preparedPost = new \stdClass();
        
        // Post ID.
        if (isset($args['id'])) {
            $existingPost = get_post((int)$args['id']);
            if (empty($existingPost) || empty($existingPost->ID) ||
                BuilderiusBranchPostType::POST_TYPE !== $existingPost->post_type) {
                throw new \Exception('Invalid Branch ID.', 400);
            }

            $preparedPost = $existingPost;
        }
        //Config
        if (array_key_exists(BuilderiusBranch::SERIALIZED_NOT_COMMITTED_CONFIG_GRAPHQL, $args)) {
            $config = json_decode($args[BuilderiusBranch::SERIALIZED_NOT_COMMITTED_CONFIG_GRAPHQL], true);
            if ($config !== null) {
                try {
                    $this->configChecker->check($config);
                } catch (\Exception $e) {
                    throw new \Exception(sprintf('Not Committed Config is not correct.%s', $e->getMessage()), 400);
                }
            }
            $event = new ConfigContainingEvent($config);
            $this->eventDispatcher->dispatch($event, 'builderius_commit_content_config_before_save');
            $config = $event->getConfig();

            $preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD} = $config ? : null;
        }
        if ($preparedPost->ID && isset($args[BuilderiusBranch::ACTIVE_COMMIT_NAME_GRAPHQL]) && $args[BuilderiusBranch::ACTIVE_COMMIT_NAME_GRAPHQL] !== null) {
            if ($args[BuilderiusBranch::ACTIVE_COMMIT_NAME_GRAPHQL] === '') {
                throw new \Exception('Not existing commit set for active_commit_name', 400);
            }
            $commitsPosts = $this->wpQuery->query([
                'post_type' => BuilderiusCommitPostType::POST_TYPE,
                'post_parent' => $preparedPost->ID,
                'name' => $args[BuilderiusBranch::ACTIVE_COMMIT_NAME_GRAPHQL],
                'post_status' => get_post_stati(),
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'orderby' => 'ID',
                'order' => 'DESC'
            ]);
            if (empty($commitsPosts)) {
                throw new \Exception('Not existing commit set for active_commit_name', 400);
            } else {
                $preparedPost->{BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD} = $args[BuilderiusBranch::ACTIVE_COMMIT_NAME_GRAPHQL];
            }
        }
        // Owner.
        if (isset($args[BuilderiusBranch::OWNER_ID_FIELD])) {
            /** @var \WP_Post $owner */
            $ownerPost = get_post((int)$args[BuilderiusBranch::OWNER_ID_FIELD]);
            if (!$ownerPost instanceof \WP_Post ||
                in_array($ownerPost->post_type, [BuilderiusBranchPostType::POST_TYPE, BuilderiusCommitPostType::POST_TYPE])) {
                throw new \Exception('Invalid owner ID.', 400);
            }
            $preparedPost->post_parent = (int)$ownerPost->ID;
        } elseif (!$preparedPost->post_parent) {
            throw new \Exception('Missing owner_id argument.', 400);
        }

        // Name
        if (isset($args[BuilderiusBranch::NAME_FIELD])) {
            $postsWithSameName = $this->wpQuery->query([
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'name' => $args[BuilderiusBranch::NAME_FIELD],
                'post_parent' => $preparedPost->post_parent,
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'no_found_rows' => true,
            ]);
            if (!empty($postsWithSameName)) {
                throw new \Exception('Branch with same name already exists.', 400);
            }
            $preparedPost->post_name = $args[BuilderiusBranch::NAME_FIELD];
        } elseif (!$preparedPost->post_name) {
            throw new \Exception('Missing name argument.', 400);
        }
        $preparedPost->post_type = BuilderiusBranchPostType::POST_TYPE;
        
        // Author.
        if (!empty($args['author_id'])) {
            $postAuthorId = $args['author_id'];

            if (apply_filters('builderius_get_current_user', wp_get_current_user())->ID !== $postAuthorId) {
                $user_obj = get_userdata($postAuthorId);

                if (!$user_obj) {
                    throw new \Exception('Invalid author ID.', 400);
                }
            }

            $preparedPost->post_author = $postAuthorId;
        } elseif (!property_exists($preparedPost, 'post_author') || !$preparedPost->post_author) {
            $preparedPost->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        }

        if (isset($ownerPost)) {
            if (array_key_exists(BuilderiusBranch::BASE_BRANCH_NAME_GRAPHQL, $args)) {
                $baseBranchPosts = $this->wpQuery->query([
                    'name' => $args[BuilderiusBranch::BASE_BRANCH_NAME_GRAPHQL],
                    'post_type' => BuilderiusBranchPostType::POST_TYPE,
                    'post_parent' => $ownerPost->ID,
                    'post_status' => get_post_stati(),
                    'posts_per_page' => 1,
                    'no_found_rows' => true,
                ]);
                if (!empty($baseBranchPosts)) {
                    $baseBranchPost = reset($baseBranchPosts);
                    $baseBranch = $this->branchFactory->createBranch($baseBranchPost);
                    $owner = $baseBranch->getOwner();
                    if ($owner->getActiveBranchName() !== $baseBranch->getName()) {
                        throw new \Exception('New branch can be created just from active branch', 400);
                    }
                }
                $preparedPost->{BuilderiusBranch::BASE_BRANCH_NAME_FIELD} =
                    $args[BuilderiusBranch::BASE_BRANCH_NAME_GRAPHQL];
                if (array_key_exists(BuilderiusBranch::BASE_COMMIT_NAME_GRAPHQL, $args)) {
                    if (isset($baseBranch)) {
                        if ($baseBranch->getActiveCommitName() !== $args[BuilderiusBranch::BASE_COMMIT_NAME_GRAPHQL]) {
                            throw new \Exception('New branch can be created just from active commit', 400);
                        }
                        $baseCommit = $baseBranch->getCommit($args[BuilderiusBranch::BASE_COMMIT_NAME_GRAPHQL]);
                        if ($baseCommit) {
                            $preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD} = $baseCommit->getContentConfig();
                            $preparedPost->{BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD} = $args[BuilderiusBranch::BASE_COMMIT_NAME_GRAPHQL];
                        }
                    }
                    $preparedPost->{BuilderiusBranch::BASE_COMMIT_NAME_FIELD} =
                        $args[BuilderiusBranch::BASE_COMMIT_NAME_GRAPHQL];
                }
            }
        }

        return $preparedPost;
    }

    /**
     * @param int $postId
     * @param int $currUserId
     * @return \WP_Post
     */
    protected function createBranchHeadCommitPost($postId, $currUserId)
    {
        $branchHeadCommitPost = new \stdClass();
        $branchHeadCommitPost->post_name = sprintf('branch_%d_user_%d', $postId, $currUserId);
        $branchHeadCommitPost->post_type = BuilderiusBranchHeadCommitPostType::POST_TYPE;
        $branchHeadCommitPost->post_parent = $postId;
        $branchHeadCommitPost->post_author = $currUserId;
        $branchHeadCommitPostId = wp_insert_post(wp_slash((array)$branchHeadCommitPost), true);

        return get_post($branchHeadCommitPostId);
    }
}