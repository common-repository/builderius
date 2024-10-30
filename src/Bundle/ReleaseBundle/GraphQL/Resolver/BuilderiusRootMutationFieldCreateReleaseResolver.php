<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\DeliverableBundle\Event\BuilderiusDSMPostCreationEvent;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModule;
use Builderius\Bundle\DeliverableBundle\Registration\BulderiusDeliverableSubModulePostType;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Model\BuilderiusRelease;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner\BuilderiusVCSOwnerFromPostFactoryInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldCreateReleaseResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusVCSOwnerFromPostFactoryInterface
     */
    private $vcsOwnerFromPostFactory;

    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $releaseFromPostFactory;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusRootMutationFieldPublishReleaseResolver
     */
    private $releasePublishMutation;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusVCSOwnerFromPostFactoryInterface $vcsOwnerFromPostFactory
     * @param BuilderiusReleaseFromPostFactory $releaseFromPostFactory
     * @param \WP_Query $wpQuery
     * @param BuilderiusRootMutationFieldPublishReleaseResolver $releasePublishMutation
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusVCSOwnerFromPostFactoryInterface $vcsOwnerFromPostFactory,
        BuilderiusReleaseFromPostFactory $releaseFromPostFactory,
        \WP_Query $wpQuery,
        BuilderiusRootMutationFieldPublishReleaseResolver $releasePublishMutation
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->vcsOwnerFromPostFactory = $vcsOwnerFromPostFactory;
        $this->releaseFromPostFactory = $releaseFromPostFactory;
        $this->wpQuery = $wpQuery;
        $this->releasePublishMutation = $releasePublishMutation;
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
        return 'createRelease';
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
        $tag = $input[BuilderiusRelease::TAG_FIELD];
        $entitiesData = json_decode($input['serialized_entities_data'], true);
        if (empty($entitiesData)) {
            throw new \Exception('Release can\'t be empty.', 400);
        }
        $postsWithSameTag = $this->wpQuery->query([
            'post_type' => BulderiusReleasePostType::POST_TYPE,
            'title' => $tag,
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'post_status' => get_post_stati()
        ]);
        if (!empty($postsWithSameTag)) {
            throw new \Exception('Release with same name already exists.', 400);
        }
        $ids = [];
        foreach ($entitiesData as $entityData) {
            $ids[] = $entityData['id'];
        }
        $entitiesPosts = $this->wpQuery->query([
            'post_type' => [BuilderiusTemplatePostType::POST_TYPE, BuilderiusGlobalSettingsSetPostType::POST_TYPE],
            'post__in' => $ids,
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'post_status' => get_post_stati(),
        ]);
        if (empty($entitiesPosts)) {
            throw new \Exception('Release can\'t be empty.' , 400);
        }
        $commits = [];
        foreach ($entitiesPosts as $post) {
            $vcsOwner = $this->vcsOwnerFromPostFactory->createOwner($post);
            $branch = $vcsOwner->getActiveBranch();
            $commit = $branch->getActiveCommit();
            if ($commit) {
                $commits[] = $commit;
            }
        }
        $preparedPost = new \stdClass;
        $preparedPost->post_title = $tag;
        $preparedPost->post_type = BulderiusReleasePostType::POST_TYPE;
        $preparedPost->post_excerpt = $input[BuilderiusRelease::DESCRIPTION_FIELD];
        $preparedPost->post_status = 'draft';
        $preparedPost->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        $preparedPost->post_date = current_time( 'mysql' );
        $preparedPost->post_date_gmt = $preparedPost->post_date;

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
        foreach ($commits as $commit) {
            $commitPost = get_post($commit->getId());
            $this->createDSMPost($post->ID, $tag, $commit, $commitPost->post_content);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_release_created');

        $release = $this->releaseFromPostFactory->createRelease($post);
        if (isset($input['publish']) && $input['publish'] == false) {
            return ['release' => $release];
        } else {
            return $this->releasePublishMutation->resolve($objectValue, ['id' => $release->getId()], $context, $info);
        }
    }

    /**
     * @param int $releaseId
     * @param string $tag
     * @param BuilderiusCommit $commit
     * @param string $content
     */
    private function createDSMPost($releaseId, $tag, BuilderiusCommit $commit, $content)
    {
        $commitOwner = $commit->getBranch()->getOwner();
        $event = new BuilderiusDSMPostCreationEvent($commitOwner, $tag);
        $this->eventDispatcher->dispatch($event, 'builderius_dsm_post_creation');
        $preparedPost = new \stdClass;
        $preparedPost->post_type = BulderiusDeliverableSubModulePostType::POST_TYPE;
        $preparedPost->post_title = $event->getTitle();
        $preparedPost->post_name = $event->getTitle();
        $preparedPost->post_excerpt = json_encode([
            BuilderiusDeliverableSubModule::TYPE_FIELD => $event->getType(),
            BuilderiusDeliverableSubModule::TECHNOLOGY_FIELD => $commitOwner->getTechnology(),
            BuilderiusDeliverableSubModule::ENTITY_TYPE_FIELD => $event->getEntityType()
        ]);
        $preparedPost->post_parent = $releaseId;
        $preparedPost->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        $preparedPost->post_date = current_time( 'mysql' );
        $preparedPost->post_date_gmt = $preparedPost->post_date;

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
        $post->post_content = $content;
        remove_all_filters('content_save_pre');
        wp_update_post($post);
        update_post_meta(
            $postId,
            BuilderiusDeliverableSubModule::CONTENT_CONFIG_FIELD,
            wp_slash(json_encode($commit->getContentConfig(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
        );
        update_post_meta(
            $postId,
            BuilderiusDeliverableSubModule::ATTRIBUTES_FIELD,
            wp_slash(json_encode($event->getAttributes(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
        );
    }
}