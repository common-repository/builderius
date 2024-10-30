<?php

namespace Builderius\Bundle\SavedFragmentBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteSavedFragmentResolver  implements GraphQLFieldResolverInterface
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
        return 'deleteSavedFragment';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeDeleted = get_post((int)$args['id']);
        if (empty($postToBeDeleted) || empty($postToBeDeleted->ID) ||
            BuilderiusSavedFragmentPostType::POST_TYPE !== $postToBeDeleted->post_type) {
            throw new \Exception('Invalid Builderius Saved Fragment ID.', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDeleted), 'builderius_saved_fragment_before_delete');

        $id = $postToBeDeleted->ID;
        $deletedPost = wp_delete_post($id, true);
        if ($deletedPost instanceof \WP_Error) {
            throw new \Exception($deletedPost->get_error_message(), 400);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($deletedPost), 'builderius_saved_fragment_deleted');

        return new \ArrayObject(['result' => true, 'message' => 'Saved Fragment was deleted successfully.']);
    }
}