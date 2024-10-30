<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootMutationFieldCreateBranchResolver extends AbstractBuilderiusRootMutationFieldCUBranchResolver
{
    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'createBranch';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        $preparedPost = $this->getPreparedPost($input);

        $preparedPost->post_type = BuilderiusBranchPostType::POST_TYPE;
        $time = current_time( 'mysql' );
        $preparedPost->post_date = $time;
        $preparedPost->post_date_gmt = get_gmt_from_date($time);
        $event = new ObjectContainingEvent($preparedPost);
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_branch_before_create'
        );

        if ($event->getError() && is_wp_error($event->getError())) {
            throw new \Exception($event->getError()->get_error_message(), 400);
        }
        $preparedPost = $event->getObject();
        $postId = wp_insert_post(wp_slash((array)$preparedPost), true);
        $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;

        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        if (property_exists($preparedPost, BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD) && $preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD} !== null) {
            $branchHeadCommitPost = $this->createBranchHeadCommitPost($postId, $currUserId);
            update_post_meta(
                $branchHeadCommitPost->ID,
                BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                wp_slash(json_encode($preparedPost->{BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD}, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
            );
            if (property_exists($preparedPost, BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD)) {
                update_post_meta(
                    $branchHeadCommitPost->ID,
                    BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD,
                    $preparedPost->{BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD}
                );
            }
            $this->eventDispatcher->dispatch(new PostContainingEvent($branchHeadCommitPost), 'builderius_branch_head_commit_created');
        }
        if (property_exists($preparedPost, BuilderiusBranch::BASE_BRANCH_NAME_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusBranch::BASE_BRANCH_NAME_FIELD,
                $preparedPost->{BuilderiusBranch::BASE_BRANCH_NAME_FIELD}
            );
        }
        if (property_exists($preparedPost, BuilderiusBranch::BASE_COMMIT_NAME_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusBranch::BASE_COMMIT_NAME_FIELD,
                $preparedPost->{BuilderiusBranch::BASE_COMMIT_NAME_FIELD}
            );
        }
        $post = get_post($postId);
        $activeBranchNameString = get_post_meta($post->post_parent, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, true);
        $activeBranchNameJson = json_decode($activeBranchNameString, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($activeBranchNameJson[$currUserId])) {
                $activeBranchNameJson[$currUserId] = $post->post_name;
            }
        } else {
            $activeBranchNameJson = [];
            $activeBranchNameJson[$currUserId] = $post->post_name;
        }
        update_post_meta(
            $post->post_parent,
            BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD,
            json_encode($activeBranchNameJson)
        );
        /*update_post_meta(
            $postId,
            BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD,
            json_encode($activeCommitNameJson)
        );*/

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_branch_created');

        $branch = $this->branchFactory->createBranch($post);

        return new \ArrayObject(['branch' => $branch]);
    }
}