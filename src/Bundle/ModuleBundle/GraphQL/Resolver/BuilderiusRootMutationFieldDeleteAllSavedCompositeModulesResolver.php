<?php

namespace Builderius\Bundle\ModuleBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ModuleBundle\Registration\BuilderiusSavedCompositeModulePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteAllSavedCompositeModulesResolver  implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \WP_Query
     */
    protected $wpQuery;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        \WP_Query $wpQuery
    ) {
        $this->eventDispatcher = $eventDispatcher;
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
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'deleteAllSavedCompositeModules';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $queryArgs = [
            'post_type' => BuilderiusSavedCompositeModulePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        if (isset($args['technology'])) {
            $queryArgs['tax_query'][] = [
                'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                'field' => 'slug',
                'include_children' => false,
                'terms' => [$args['technology']]
            ];
        }
        $posts = $this->wpQuery->query($queryArgs);
        foreach ($posts as $postToBeDeleted) {
            $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDeleted), 'builderius_saved_composite_module_before_delete');

            $id = $postToBeDeleted->ID;
            $deletedPost = wp_delete_post($id, true);
            if ($deletedPost instanceof \WP_Error) {
                throw new \Exception($deletedPost->get_error_message(), 400);
            }

            $this->eventDispatcher->dispatch(new PostContainingEvent($deletedPost), 'builderius_saved_composite_module_deleted');
        }

        return new \ArrayObject(['result' => true, 'message' => 'All Composite Modules were deleted successfully.']);
    }
}