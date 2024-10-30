<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldUnpublishReleaseResolver implements GraphQLFieldResolverInterface
{

    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $releaseFromPostFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param BuilderiusReleaseFromPostFactory $releaseFromPostFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        BuilderiusReleaseFromPostFactory $releaseFromPostFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->releaseFromPostFactory = $releaseFromPostFactory;
        $this->eventDispatcher = $eventDispatcher;
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
        return 'unpublishRelease';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeUnPublished = get_post((int)$args['id']);
        if (empty($postToBeUnPublished) || empty($postToBeUnPublished->ID) ||
            BulderiusReleasePostType::POST_TYPE !== $postToBeUnPublished->post_type) {
            throw new \Exception('Invalid Release ID.', 400);
        }
        if (in_array($postToBeUnPublished->post_status, ['draft'])) {
            throw new \Exception('Release is not published', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeUnPublished), 'builderius_release_before_unpublish');
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeUnPublished), 'builderius_deliverable_before_unpublish');

        $postToBeUnPublished->post_status = 'draft';
        $unpublishedPostId = wp_update_post($postToBeUnPublished);
        if ($unpublishedPostId instanceof \WP_Error) {
            throw new \Exception($unpublishedPostId->get_error_message(), 400);
        }

        $unPublishedPost = get_post((int)$unpublishedPostId);
        $this->eventDispatcher->dispatch(new PostContainingEvent($unPublishedPost), 'builderius_release_unpublished');
        $this->eventDispatcher->dispatch(new PostContainingEvent($unPublishedPost), 'builderius_deliverable_unpublished');

        $release = $this->releaseFromPostFactory->createRelease($unPublishedPost);

        return ['release' => $release];
    }
}