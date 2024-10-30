<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootMutationFieldUpdateBranchResolver extends AbstractBuilderiusRootMutationFieldCUBranchResolver
{
    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'updateBranch';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        $existingPost = get_post((int)$input['id']);
        if (empty($existingPost) || empty($existingPost->ID) ||
            BuilderiusBranchPostType::POST_TYPE !== $existingPost->post_type) {
            throw new \Exception('Invalid Branch ID.', 400);
        }
        if (array_key_exists(BuilderiusBranch::ACTIVE_COMMIT_NAME_GRAPHQL, $input) && array_key_exists(BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD, $input)) {
            throw new \Exception('It it not correct to set active_commit_name and not_committed_config at the same time', 400);
        }
        if (isset($input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD]) && $input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD] !== null) {
            if ($input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD] === '') {
                throw new \Exception('Not existing commit set for ncc_base_commit_name', 400);
            }
            $commitsPosts = $this->wpQuery->query([
                'post_type' => BuilderiusCommitPostType::POST_TYPE,
                'post_parent' => $existingPost->ID,
                'name' => $input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD],
                'post_status' => get_post_stati(),
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'orderby' => 'ID',
                'order' => 'DESC'
            ]);
            if (empty($commitsPosts)) {
                throw new \Exception('Not existing commit set for ncc_base_commit_name', 400);
            }
        }
        $preparedPost = $this->getPreparedPost($input);
        $postId = $preparedPost->ID;
        $currUserHeadCommitsPosts = $this->wpQuery->query([
            'post_type' => BuilderiusBranchHeadCommitPostType::POST_TYPE,
            'post_parent' => $postId,
            'name' => sprintf('branch_%d_user_%d', $postId, $currUserId),
            'author' => $currUserId,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'orderby' => 'ID',
            'order' => 'DESC'
        ]);
        if (!empty($currUserHeadCommitsPosts)) {
            $branchHeadCommitPost = reset($currUserHeadCommitsPosts);
        }
        if (property_exists($preparedPost, BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD) && $preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD} !== null) {
            if (isset($branchHeadCommitPost)) {
                update_post_meta(
                    $branchHeadCommitPost->ID,
                    BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                    wp_slash(json_encode($preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD}, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
                );
                if (isset($input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD]) && $input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD] !== null) {
                    update_post_meta(
                        $branchHeadCommitPost->ID,
                        BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD,
                        $input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD]
                    );
                }
                $this->eventDispatcher->dispatch(new PostContainingEvent($branchHeadCommitPost), 'builderius_branch_head_commit_updated');
            } else {
                $branchHeadCommitPost = $this->createBranchHeadCommitPost($postId, $currUserId);
                update_post_meta(
                    $branchHeadCommitPost->ID,
                    BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                    wp_slash(json_encode($preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD}, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
                );
                if (isset($input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD]) && $input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD] !== null) {
                    update_post_meta(
                        $branchHeadCommitPost->ID,
                        BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD,
                        $input[BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD]
                    );
                }
                $this->eventDispatcher->dispatch(new PostContainingEvent($branchHeadCommitPost), 'builderius_branch_head_commit_created');
            }
            $activeCommitNameString = get_post_meta($postId, BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD, true);
            $activeCommitNameJson = json_decode($activeCommitNameString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($activeCommitNameJson[$currUserId])) {
                    unset ($activeCommitNameJson[$currUserId]);
                }
            } else {
                $activeCommitNameJson = [];
            }
            update_post_meta(
                $postId,
                BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD,
                json_encode($activeCommitNameJson)
            );
        }
        if (property_exists($preparedPost, BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD)) {
            $activeCommitNameString = get_post_meta($postId, BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD, true);
            $activeCommitNameJson = json_decode($activeCommitNameString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $activeCommitNameJson[$currUserId] = $preparedPost->{BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD};
            } else {
                $activeCommitNameJson = [];
                $activeCommitNameJson[$currUserId] = $preparedPost->{BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD};
            }
            $activeBranchNameString = get_post_meta($preparedPost->post_parent, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, true);
            $activeBranchNameJson = json_decode($activeBranchNameString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $activeBranchNameJson[$currUserId] = $preparedPost->post_name;
            } else {
                $activeBranchNameJson = [];
                $activeBranchNameJson[$currUserId] = $preparedPost->post_name;
            }
            update_post_meta(
                $preparedPost->post_parent,
                BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD,
                json_encode($activeBranchNameJson)
            );
            update_post_meta(
                $postId,
                BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD,
                json_encode($activeCommitNameJson)
            );
            if (isset($branchHeadCommitPost)) {
                wp_delete_post($branchHeadCommitPost->ID, true);
            }
        }
        $post = get_post($postId);

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_branch_updated');

        $branch = $this->branchFactory->createBranch($post);

        return new \ArrayObject(['branch' => $branch]);
    }
}