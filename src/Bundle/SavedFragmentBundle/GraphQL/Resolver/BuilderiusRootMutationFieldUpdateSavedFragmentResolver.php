<?php

namespace Builderius\Bundle\SavedFragmentBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\SavedFragmentBundle\Factory\BuilderiusSavedFragmentFromPostFactory;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragment;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentCategoryTaxonomy;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentTagTaxonomy;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldUpdateSavedFragmentResolver implements GraphQLFieldResolverInterface
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
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateContentConfigCheckerInterface $configChecker
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateContentConfigCheckerInterface $configChecker,
        \WP_Query $wpQuery
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configChecker = $configChecker;
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
        return 'updateSavedFragment';
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
            BuilderiusSavedFragmentPostType::POST_TYPE !== $existingPost->post_type) {
            throw new \Exception('Invalid Saved Fragment ID.', 400);
        }
        $preparedPost = $this->getPreparedPost($input, $existingPost);

        $time = current_time( 'mysql' );
        $preparedPost->post_date = $time;
        $preparedPost->post_date_gmt = get_gmt_from_date($time);
        $event = new ObjectContainingEvent($preparedPost);
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_saved_fragment_before_update'
        );

        if ($event->getError() && is_wp_error($event->getError())) {
            throw new \Exception($event->getError()->get_error_message(), 400);
        }
        $preparedPost = $event->getObject();
        $postId = wp_update_post(wp_slash((array)$preparedPost), true);
        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        $post = get_post($postId);
        if (property_exists($preparedPost, BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD,
                wp_slash($preparedPost->{BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD})
            );
        }
        if (property_exists($preparedPost, BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD,
                wp_slash($preparedPost->{BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD})
            );
        }
        if (property_exists($preparedPost, BuilderiusSavedFragment::IMAGE_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedFragment::IMAGE_FIELD,
                $preparedPost->{BuilderiusSavedFragment::IMAGE_FIELD}
            );
        }
        if (isset($input['category'])) {
            $categoryTermsUpdate = $this->handleTerms(
                $post->ID,
                [$preparedPost->{BuilderiusSavedFragment::CATEGORY_FIELD}],
                BuilderiusSavedFragmentCategoryTaxonomy::NAME
            );
            if (is_wp_error($categoryTermsUpdate)) {
                throw new \Exception($categoryTermsUpdate->get_error_message(), 400);
            }
        }
        if (isset($input['tags'])) {
            $tagsTermsUpdate = $this->handleTerms(
                $post->ID,
                $preparedPost->tags,
                BuilderiusSavedFragmentTagTaxonomy::NAME
            );
            if (is_wp_error($tagsTermsUpdate)) {
                throw new \Exception($tagsTermsUpdate->get_error_message(), 400);
            }
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_saved_fragment_updated');

        return new \ArrayObject(['saved_fragment' => BuilderiusSavedFragmentFromPostFactory::createSavedFragment($post)]);
    }

    /**
     * @param array $args
     * @param \WP_Post $post
     * @return \WP_Post
     * @throws \Exception
     */
    protected function getPreparedPost(array $args, \WP_Post $post)
    {
        $prepared_post = $post;
        if (isset($args[BuilderiusSavedFragment::NAME_FIELD])) {
            $postsWithSameName = $this->wpQuery->query([
                'post_type' => BuilderiusSavedFragmentPostType::POST_TYPE,
                'name' => $args[BuilderiusSavedFragment::NAME_FIELD],
                'post__not_in' => [$post->ID],
                'posts_per_page' => 1,
                'no_found_rows' => true,
                'post_status' => get_post_stati()
            ]);
            if (!empty($postsWithSameName)) {
                throw new \Exception('Saved Fragment with same name already exists.', 400);
            }
            $prepared_post->post_name = $args[BuilderiusSavedFragment::NAME_FIELD];
        }
        if (isset($args[BuilderiusSavedFragment::TITLE_FIELD])) {
            $postsWithSameTitle = $this->wpQuery->query([
                'post_type' => BuilderiusSavedFragmentPostType::POST_TYPE,
                'title' => $args[BuilderiusSavedFragment::TITLE_FIELD],
                'post__not_in' => [$post->ID],
                'posts_per_page' => 1,
                'no_found_rows' => true,
                'post_status' => get_post_stati()
            ]);
            if (!empty($postsWithSameTitle)) {
                throw new \Exception('Saved Fragment with same title already exists.', 400);
            }
            $prepared_post->post_title = $args[BuilderiusSavedFragment::TITLE_FIELD];
            if (!isset($args[BuilderiusSavedFragment::NAME_FIELD])) {
                $prepared_post->post_name = $prepared_post->post_title;
            }
        }
        //Description
        if (isset($args[BuilderiusSavedFragment::DESCRIPTION_FIELD])) {
            $prepared_post->post_content = $args[BuilderiusSavedFragment::DESCRIPTION_FIELD];
        }

        //Static Config
        if (array_key_exists(BuilderiusSavedFragment::SERIALIZED_STATIC_CONTENT_CONFIG_GRAPHQL, $args)) {
            $config = json_decode($args[BuilderiusSavedFragment::SERIALIZED_STATIC_CONTENT_CONFIG_GRAPHQL], true);
            if ($config !== null) {
                $config['template']['type'] = 'template';
                $config['template']['technology'] = $args['technology'];
                try {
                    $this->configChecker->check($config);
                } catch (\Exception $e) {
                    throw new \Exception(sprintf('Static Content Config is not valid. %s', $e->getMessage()), 400);
                }
            } else {
                throw new \Exception('Static Content Config is not valid.', 400);
            }
            $event = new ConfigContainingEvent($config);
            $this->eventDispatcher->dispatch($event, 'builderius_saved_fragment_content_config_before_save');
            $config = $event->getConfig();
            //unset($config['template']);
            $prepared_post->{BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD} = $config ? json_encode($config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE) : null;
        }
        //Dynamic Config
        if (array_key_exists(BuilderiusSavedFragment::SERIALIZED_DYNAMIC_CONTENT_CONFIG_GRAPHQL, $args)) {
            $config = json_decode($args[BuilderiusSavedFragment::SERIALIZED_DYNAMIC_CONTENT_CONFIG_GRAPHQL], true);
            if ($config !== null) {
                $config['template']['type'] = 'template';
                $config['template']['technology'] = $args['technology'];
                try {
                    $this->configChecker->check($config);
                } catch (\Exception $e) {
                    throw new \Exception(sprintf('Dynamic Content Config is not valid. %s', $e->getMessage()), 400);
                }
            } else {
                throw new \Exception('Dynamic Content Config is not valid.', 400);
            }
            $event = new ConfigContainingEvent($config);
            $this->eventDispatcher->dispatch($event, 'builderius_saved_fragment_content_config_before_save');
            $config = $event->getConfig();
            //unset($config['template']);
            $prepared_post->{BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD} = $config ? json_encode($config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE) : null;
        }
        // Image.
        if (isset($args['image'])) {
            $prepared_post->image = $args['image'];
        }
        // Category.
        if (isset($args['category'])) {
            if (!term_exists($args['category'], BuilderiusSavedFragmentCategoryTaxonomy::NAME)) {
                throw new \Exception('Not existing "category".', 400);
            }
            $prepared_post->category = $args['category'];
        } else {
            throw new \Exception('Missing required parameter "category".', 400);
        }
        // Tags.
        if (isset($args['tags'])) {
            $prepared_post->tags = $args['tags'];
        }
        $prepared_post->post_type = BuilderiusSavedFragmentPostType::POST_TYPE;

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
        } elseif (!$prepared_post->post_author) {
            $prepared_post->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        }

        return $prepared_post;
    }

    /**
     * @param int $post_id
     * @param array $values
     * @param string $taxonomyName
     * @return array|bool|int|int[]|\WP_Error|null
     */
    protected function handleTerms($post_id, array $values, $taxonomyName)
    {
        $result = wp_set_object_terms($post_id, $values, $taxonomyName);

        if (is_wp_error($result)) {
            return $result;
        }

        return null;
    }
}