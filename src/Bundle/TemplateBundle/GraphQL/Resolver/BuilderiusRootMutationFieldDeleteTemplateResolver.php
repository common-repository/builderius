<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteTemplateResolver  implements GraphQLFieldResolverInterface
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
        return 'deleteTemplate';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeDeleted = get_post((int)$args['id']);
        if (empty($postToBeDeleted) || empty($postToBeDeleted->ID) ||
            BuilderiusTemplatePostType::POST_TYPE !== $postToBeDeleted->post_type) {
            throw new \Exception('Invalid Template ID.', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDeleted), 'builderius_template_before_delete');

        $id = $postToBeDeleted->ID;
        $deletedPost = wp_delete_post($id, true);
        if ($deletedPost instanceof \WP_Error) {
            throw new \Exception($deletedPost->get_error_message(), 400);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($deletedPost), 'builderius_template_deleted');

        return new \ArrayObject(['result' => true, 'message' => 'Template was deleted successfully.']);
    }
}