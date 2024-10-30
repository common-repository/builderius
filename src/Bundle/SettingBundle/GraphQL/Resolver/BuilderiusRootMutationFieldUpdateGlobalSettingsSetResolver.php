<?php

namespace Builderius\Bundle\SettingBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldUpdateGlobalSettingsSetResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $gssFactory;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFactory;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusGlobalSettingsSetFromPostFactory $gssFactory
     * @param \WP_Query $wpQuery
     * @param BuilderiusBranchFromPostFactory $branchFactory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusGlobalSettingsSetFromPostFactory $gssFactory,
        \WP_Query $wpQuery,
        BuilderiusBranchFromPostFactory $branchFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->gssFactory = $gssFactory;
        $this->wpQuery = $wpQuery;
        $this->branchFactory = $branchFactory;
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
        return 'updateTemplate';
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
        $input = $args['input'];
        $existingPost = get_post((int)$input['id']);
        if (empty($existingPost) || empty($existingPost->ID) ||
            BuilderiusGlobalSettingsSetPostType::POST_TYPE !== $existingPost->post_type) {
            throw new \Exception('Invalid GlobalSettingsSet ID.', 400);
        }
        if (array_key_exists(BuilderiusTemplate::ACTIVE_BRANCH_NAME_GRAPHQL, $input)) {
            $activeBranchName = $input[BuilderiusTemplate::ACTIVE_BRANCH_NAME_GRAPHQL];
            $activeBranchPosts = $this->wpQuery->query([
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'name' => $activeBranchName,
                'post_parent' => $existingPost->ID,
                'post_status' => get_post_stati(),
                'posts_per_page' => -1,
                'no_found_rows' => true,
            ]);
            if (count($activeBranchPosts) === 1) {
                $activeBranchPost = reset($activeBranchPosts);
                $activeBranch = $this->branchFactory->createBranch($activeBranchPost);
                $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
                $existingActiveBranchNameString = get_post_meta($existingPost->ID, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, true);
                $activeBranchNameJson = json_decode($existingActiveBranchNameString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $activeBranchNameJson[$currUserId] = $activeBranchName;
                } else {
                    $activeBranchNameJson = [];
                    $activeBranchNameJson[$currUserId] = $activeBranchName;
                }
                update_post_meta($existingPost->ID, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, json_encode($activeBranchNameJson));
                $commitsPosts = $this->wpQuery->query([
                    'post_type' => BuilderiusCommitPostType::POST_TYPE,
                    'post_parent' => $activeBranchPost->ID,
                    'post_status' => get_post_stati(),
                    'posts_per_page' => -1,
                    'no_found_rows' => true,
                ]);
                if (empty($commitsPosts)) {
                    $baseCommit = $activeBranch->getBaseCommit();
                    if ($baseCommit) {
                        $branchHeadCommitPost = $this->createBranchHeadCommitPost($activeBranch->getId(), $currUserId);
                        update_post_meta(
                            $branchHeadCommitPost->ID,
                            BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                            wp_slash(json_encode($baseCommit->getContentConfig(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
                        );
                        update_post_meta(
                            $branchHeadCommitPost->ID,
                            BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD,
                            $baseCommit->getName()
                        );
                    }
                }
            }
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($existingPost), 'builderius_global_settings_set_updated');

        $gss = $this->gssFactory->createGlobalSettingsSet($existingPost);

        return ['global_settings_sets' => $gss];
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