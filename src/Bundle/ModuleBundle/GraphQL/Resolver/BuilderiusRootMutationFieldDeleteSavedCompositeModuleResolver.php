<?php

namespace Builderius\Bundle\ModuleBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ModuleBundle\Registration\BuilderiusSavedCompositeModulePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteSavedCompositeModuleResolver  implements GraphQLFieldResolverInterface
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
        return 'deleteSavedCompositeModule';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeDeleted = get_post((int)$args['id']);
        if (empty($postToBeDeleted) || empty($postToBeDeleted->ID) ||
            BuilderiusSavedCompositeModulePostType::POST_TYPE !== $postToBeDeleted->post_type) {
            throw new \Exception('Invalid Builderius Composite Module ID.', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDeleted), 'builderius_saved_composite_module_before_delete');

        $id = $postToBeDeleted->ID;
        $deletedPost = wp_delete_post($id, true);
        if ($deletedPost instanceof \WP_Error) {
            throw new \Exception($deletedPost->get_error_message(), 400);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($deletedPost), 'builderius_saved_composite_module_deleted');

        return new \ArrayObject(['result' => true, 'message' => 'Composite Module was deleted successfully.']);
    }
}