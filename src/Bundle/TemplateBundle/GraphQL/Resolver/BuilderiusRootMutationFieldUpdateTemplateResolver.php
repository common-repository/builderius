<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateSubTypeTaxonomy;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldUpdateTemplateResolver extends AbstractBuilderiusRootMutationFieldCUTemplateResolver
{
    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFactory;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateFromPostFactory $templateFactory
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateFromPostFactory $templateFactory,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        \WP_Query $wpQuery,
        BuilderiusBranchFromPostFactory $branchFactory
    ) {
        parent::__construct($eventDispatcher, $templateFactory, $templateTypesProvider, $wpQuery);
        $this->branchFactory = $branchFactory;
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
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        if (isset($input[BuilderiusTemplate::NAME_FIELD])) {
            $postsWithSameName = $this->wpQuery->query([
                'post_type' => BuilderiusTemplatePostType::POST_TYPE,
                'name' => $input[BuilderiusTemplate::NAME_FIELD],
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'no_found_rows' => true,
            ]);
            if (!empty($postsWithSameName)) {
                foreach ($postsWithSameName as $postWithSameName) {
                    if ($postWithSameName->ID !== (int)$input['id']) {
                        throw new \Exception('Template with same name already exists.', 400);
                    }
                }
            }
        }
        if (isset($input[BuilderiusTemplate::TITLE_FIELD])) {
            $postsWithSameTitle = $this->wpQuery->query([
                'post_type' => BuilderiusTemplatePostType::POST_TYPE,
                'title' => $input[BuilderiusTemplate::TITLE_FIELD],
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'no_found_rows' => true,
            ]);
            if (!empty($postsWithSameTitle)) {
                foreach ($postsWithSameTitle as $postWithSameTitle) {
                    if ($postWithSameTitle->ID !== (int)$input['id']) {
                        throw new \Exception('Template with same title already exists.', 400);
                    }
                }
            }
        }
        $existingPost = get_post((int)$input['id']);
        if (empty($existingPost) || empty($existingPost->ID) ||
            BuilderiusTemplatePostType::POST_TYPE !== $existingPost->post_type) {
            throw new \Exception('Invalid Template ID.', 400);
        }
        $existingTemplate = $this->templateFactory->createTemplate($existingPost);
        $preparedPost = $this->getPreparedPost($input);

        $postId = wp_insert_post(wp_slash((array)$preparedPost), true);

        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        if (array_key_exists(BuilderiusTemplate::SORT_ORDER_FIELD, $input)) {
            update_post_meta(
                $postId,
                BuilderiusTemplate::SORT_ORDER_FIELD,
                $preparedPost->{BuilderiusTemplate::SORT_ORDER_FIELD}
            );
        }
        if (array_key_exists(BuilderiusTemplate::SERIALIZED_APPLY_RULES_CONFIG_GRAPHQL, $input)) {
            update_post_meta(
                $postId,
                BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD,
                $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD}
            );
        }
        if (array_key_exists(BuilderiusTemplate::ACTIVE_BRANCH_NAME_GRAPHQL, $input)) {
            $activeBranchName = $input[BuilderiusTemplate::ACTIVE_BRANCH_NAME_GRAPHQL];
            $activeBranchPosts = $this->wpQuery->query([
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'name' => $activeBranchName,
                'post_parent' => $preparedPost->ID,
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'no_found_rows' => true,
            ]);
            if (count($activeBranchPosts) === 1) {
                $activeBranchPost = reset($activeBranchPosts);
                $activeBranch = $this->branchFactory->createBranch($activeBranchPost);
                $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
                $existingActiveBranchNameString = get_post_meta($postId, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, true);
                $activeBranchNameJson = json_decode($existingActiveBranchNameString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $activeBranchNameJson[$currUserId] = $activeBranchName;
                } else {
                    $activeBranchNameJson = [];
                    $activeBranchNameJson[$currUserId] = $activeBranchName;
                }
                update_post_meta($postId, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, json_encode($activeBranchNameJson));
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
        if ($existingTemplate->getType() === 'template') {
            if ($input[BuilderiusTemplate::SUB_TYPE_FIELD] === 'regular') {
                if (
                    $existingTemplate->getSubType() !== 'regular' &&
                    array_key_exists(BuilderiusTemplate::SUB_TYPE_FIELD, $input) &&
                    $input[BuilderiusTemplate::SUB_TYPE_FIELD] === 'regular'
                ) {
                    $subTypeTermsUpdate = $this->handleTerms(
                        $existingPost->ID,
                        $input[BuilderiusTemplate::SUB_TYPE_FIELD],
                        BuilderiusTemplateSubTypeTaxonomy::NAME
                    );

                    if (is_wp_error($subTypeTermsUpdate)) {
                        throw new \Exception($subTypeTermsUpdate->get_error_message(), 400);
                    }
                }
                delete_post_meta(
                    $postId,
                    BuilderiusTemplate::HOOK_FIELD
                );
                delete_post_meta(
                    $postId,
                    BuilderiusTemplate::HOOK_TYPE_FIELD
                );
                delete_post_meta(
                    $postId,
                    BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD
                );
                delete_post_meta(
                    $postId,
                    BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD
                );
            } elseif ($input[BuilderiusTemplate::SUB_TYPE_FIELD] === 'hook') {
                if (
                    $existingTemplate->getSubType() !== 'hook' &&
                    array_key_exists(BuilderiusTemplate::SUB_TYPE_FIELD, $input) &&
                    $input[BuilderiusTemplate::SUB_TYPE_FIELD] === 'hook'
                ) {
                    if (!isset($input[BuilderiusTemplate::HOOK_FIELD])) {
                        throw new \Exception('Argument "hook" is required for Hook Template', 400);
                    }
                    if (!isset($input[BuilderiusTemplate::HOOK_TYPE_FIELD])) {
                        throw new \Exception('Argument "hook_type" is required for Hook Template', 400);
                    }
                    $subTypeTermsUpdate = $this->handleTerms(
                        $existingPost->ID,
                        $input[BuilderiusTemplate::SUB_TYPE_FIELD],
                        BuilderiusTemplateSubTypeTaxonomy::NAME
                    );

                    if (is_wp_error($subTypeTermsUpdate)) {
                        throw new \Exception($subTypeTermsUpdate->get_error_message(), 400);
                    }
                }
                if (array_key_exists(BuilderiusTemplate::HOOK_FIELD, $input)) {
                    if (in_array($input[BuilderiusTemplate::HOOK_FIELD], static::FORBIDDEN_HOOKS)) {
                        throw new \Exception(sprintf('You cannot create Hook Template for hook "%s"', $input[BuilderiusTemplate::HOOK_FIELD]), 400);
                    }
                    update_post_meta(
                        $postId,
                        BuilderiusTemplate::HOOK_FIELD,
                        $input[BuilderiusTemplate::HOOK_FIELD]
                    );
                }
                if (array_key_exists(BuilderiusTemplate::HOOK_TYPE_FIELD, $input)) {
                    update_post_meta(
                        $postId,
                        BuilderiusTemplate::HOOK_TYPE_FIELD,
                        $input[BuilderiusTemplate::HOOK_TYPE_FIELD]
                    );
                }
                if (array_key_exists(BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD, $input)) {
                    update_post_meta(
                        $postId,
                        BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD,
                        $input[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD]
                    );
                }
                if (array_key_exists(BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD, $input)) {
                    update_post_meta(
                        $postId,
                        BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD,
                        $input[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD] === true ? 'true' : 'false'
                    );
                }
            }
        } else {
            $subTypeTermsUpdate = $this->handleTerms(
                $existingPost->ID,
                $input[BuilderiusTemplate::SUB_TYPE_FIELD],
                BuilderiusTemplateSubTypeTaxonomy::NAME
            );
            if (is_wp_error($subTypeTermsUpdate)) {
                throw new \Exception($subTypeTermsUpdate->get_error_message(), 400);
            }
        }
        $post = get_post($postId);
        if ($existingTemplate->getSubType() !== 'hook' && $input[BuilderiusTemplate::SUB_TYPE_FIELD] === 'hook') {
            $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_template_type_changed_to_hook');
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_template_updated');

        $template = $this->templateFactory->createTemplate($post);

        return new \ArrayObject(['template' => $template]);
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