<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldPublishReleaseResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $releaseFromPostFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var BuilderiusRootMutationFieldUnpublishReleaseResolver
     */
    private $releaseUnpublishMutation;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusReleaseFromPostFactory $releaseFromPostFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusRootMutationFieldUnpublishReleaseResolver $releaseUnpublishMutation
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusReleaseFromPostFactory $releaseFromPostFactory,
        EventDispatcherInterface $eventDispatcher,
        BuilderiusRootMutationFieldUnpublishReleaseResolver $releaseUnpublishMutation
    ) {
        $this->wpQuery = $wpQuery;
        $this->releaseFromPostFactory = $releaseFromPostFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->releaseUnpublishMutation = $releaseUnpublishMutation;
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
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'publishRelease';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBePublished = get_post((int)$args['id']);
        if (empty($postToBePublished) || empty($postToBePublished->ID) ||
            BulderiusReleasePostType::POST_TYPE !== $postToBePublished->post_type) {
            throw new \Exception('Invalid Release ID.', 400);
        }
        if (in_array($postToBePublished->post_status, ['publish', 'future'])) {
            throw new \Exception('Release is already published', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBePublished), 'builderius_release_before_publish');
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBePublished), 'builderius_deliverable_before_publish');
        $publishedBeforePosts = $this->wpQuery->query([
            'post_type' => BulderiusReleasePostType::POST_TYPE,
            'post_status' => ['publish', 'future'],
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ]);
        $postToBePublished->post_status = 'publish';
        $publishedPostId = wp_update_post($postToBePublished);
        if ($publishedPostId instanceof \WP_Error) {
            throw new \Exception($publishedPostId->get_error_message(), 400);
        }
        foreach ($publishedBeforePosts as $publishedBeforePost) {
            $this->releaseUnpublishMutation->resolve($objectValue, ['id' => $publishedBeforePost->ID], $context, $info);
        }

        $publishedPost = get_post((int)$publishedPostId);
        $this->eventDispatcher->dispatch(new PostContainingEvent($publishedPost), 'builderius_release_published');
        $this->eventDispatcher->dispatch(new PostContainingEvent($publishedPost), 'builderius_deliverable_published');

        $release = $this->releaseFromPostFactory->createRelease($publishedPost);

        return ['release' => $release];
    }
}