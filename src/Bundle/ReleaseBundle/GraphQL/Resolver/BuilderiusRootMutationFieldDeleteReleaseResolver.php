<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteReleaseResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
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
        return 'deleteRelease';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeDeleted = get_post((int)$args['id']);
        if (empty($postToBeDeleted) || empty($postToBeDeleted->ID) ||
            BulderiusReleasePostType::POST_TYPE !== $postToBeDeleted->post_type) {
            throw new \Exception('Invalid Release ID.', 400);
        }
        if (in_array($postToBeDeleted->post_status, ['publish', 'future'])) {
            throw new \Exception('Published release can\'t be deleted', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDeleted), 'builderius_release_before_delete');
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDeleted), 'builderius_deliverable_before_delete');

        $id = $postToBeDeleted->ID;
        $deletedPost = wp_delete_post($id, true);
        if ($deletedPost instanceof \WP_Error) {
            throw new \Exception($deletedPost->get_error_message(), 400);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($deletedPost), 'builderius_release_deleted');
        $this->eventDispatcher->dispatch(new PostContainingEvent($deletedPost), 'builderius_deliverable_deleted');

        return ['result' => true, 'message' => 'Release was deleted successfully.'];
    }
}