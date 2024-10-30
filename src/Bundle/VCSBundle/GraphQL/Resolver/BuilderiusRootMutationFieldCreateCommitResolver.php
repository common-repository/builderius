<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusCommitFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldCreateCommitResolver implements GraphQLFieldResolverInterface
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface
     */
    private $configChecker;

    /**
     * @var BuilderiusCommitFromPostFactory
     */
    private $commitFactory;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateContentConfigCheckerInterface $configChecker
     * @param BuilderiusCommitFromPostFactory $commitFactory
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateContentConfigCheckerInterface $configChecker,
        BuilderiusCommitFromPostFactory $commitFactory,
        \WP_Query $wpQuery
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configChecker = $configChecker;
        $this->commitFactory = $commitFactory;
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
    public function getFieldName()
    {
        return 'createCommit';
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
        $preparedPost = $this->getPreparedPost($input);
        $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;

        $time = current_time( 'mysql' );
        $preparedPost->post_date = $time;
        $preparedPost->post_date_gmt = get_gmt_from_date($time);
        $event = new ObjectContainingEvent($preparedPost);
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_commit_before_create'
        );

        if ($event->getError() && is_wp_error($event->getError())) {
            throw new \Exception($event->getError()->get_error_message(), 400);
        }
        $preparedPost = $event->getObject();
        $postId = wp_insert_post(wp_slash((array)$preparedPost), true);
        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        $post = get_post($postId);
        if (property_exists($preparedPost, BuilderiusCommit::CONTENT_CONFIG_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusCommit::CONTENT_CONFIG_FIELD,
                wp_slash($preparedPost->{BuilderiusCommit::CONTENT_CONFIG_FIELD})
            );
        }
        $currUserHeadCommitsPosts = $this->wpQuery->query([
            'post_type' => BuilderiusBranchHeadCommitPostType::POST_TYPE,
            'post_parent' => $post->post_parent,
            'name' => sprintf('branch_%d_user_%d', $post->post_parent, $currUserId),
            'author' => $currUserId,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'orderby' => 'ID',
            'order' => 'DESC'
        ]);
        if (!empty($currUserHeadCommitsPosts)) {
            $branchHeadCommitPost = reset($currUserHeadCommitsPosts);
            wp_delete_post($branchHeadCommitPost->ID, true);
        }

        $activeCommitNameString = get_post_meta($post->post_parent, BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD, true);
        $activeCommitNameJson = json_decode($activeCommitNameString, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $activeCommitNameJson[$currUserId] = $post->post_name;
        } else {
            $activeCommitNameJson = [];
            $activeCommitNameJson[$currUserId] = $post->post_name;
        }
        update_post_meta(
            $post->post_parent,
            BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD,
            json_encode($activeCommitNameJson)
        );
        if (isset($input[BuilderiusCommit::MERGED_BRANCH_NAME_FIELD]) && isset($input[BuilderiusCommit::MERGED_COMMIT_NAME_FIELD])) {
            update_post_meta(
                $post->ID,
                BuilderiusCommit::MERGED_BRANCH_NAME_FIELD,
                $input[BuilderiusCommit::MERGED_BRANCH_NAME_FIELD]
            );
            update_post_meta(
                $post->ID,
                BuilderiusCommit::MERGED_COMMIT_NAME_FIELD,
                $input[BuilderiusCommit::MERGED_COMMIT_NAME_FIELD]
            );
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_commit_created');

        $commit = $this->commitFactory->createCommit($post);

        return ['commit' => $commit];
    }

    protected function getPreparedPost(array $args)
    {
        $prepared_post = new \stdClass;

        //Config
        if (array_key_exists(BuilderiusCommit::SERIALIZED_CONTENT_CONFIG_GRAPHQL, $args)) {
            $config = json_decode($args[BuilderiusCommit::SERIALIZED_CONTENT_CONFIG_GRAPHQL], true);
            if ($config !== null) {
                try {
                    $this->configChecker->check($config);
                } catch (\Exception $e) {
                    throw new \Exception(sprintf('Content Config is not valid. %s', $e->getMessage()), 400);
                }
            } else {
                throw new \Exception('Content Config is not valid.', 400);
            }
            $event = new ConfigContainingEvent($config);
            $this->eventDispatcher->dispatch($event, 'builderius_commit_content_config_before_save');
            $config = $event->getConfig();
            $prepared_post->{BuilderiusCommit::CONTENT_CONFIG_FIELD} = $config ? json_encode($config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE) : null;
        } else {
            throw new \Exception('Missing required parameter "serialized_content_config".', 400);
        }
        // Parent.
        if (isset($args['branch_id'])) {
            /** @var \WP_Post $parent */
            $parent = get_post((int)$args['branch_id']);
            if (empty($parent)) {
                throw new \Exception('Invalid branch ID.', 400);
            }
            $prepared_post->post_parent = (int)$parent->ID;
        } else {
            throw new \Exception('Missing required parameter "branch_id".', 400);
        }

        // Name
        $prepared_post->post_name = bin2hex(random_bytes(10));
        // Description
        if (isset($args[BuilderiusCommit::DESCRIPTION_FIELD])) {
            $prepared_post->post_excerpt = $args[BuilderiusCommit::DESCRIPTION_FIELD];
        }

        $prepared_post->post_type = BuilderiusCommitPostType::POST_TYPE;

        // Post status.
        $prepared_post->post_status = 'draft';

        // Author.
        if (!empty($args['author_id'])) {
            $post_author = (int)$args['author_id'];

            if (apply_filters('builderius_get_current_user', wp_get_current_user())->ID !== $post_author) {
                $user_obj = get_userdata($post_author);

                if (!$user_obj) {
                    throw new \Exception('Invalid author ID.', 400);
                }
            }

            $prepared_post->post_author = $post_author;
        } elseif (!property_exists($prepared_post, 'post_author') || $prepared_post->post_author == null) {
            $prepared_post->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        }

        return $prepared_post;
    }
}