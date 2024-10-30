<?php

namespace Builderius\Bundle\SavedFragmentBundle\GraphQL\Resolver;

use Builderius\Bundle\CategoryBundle\Factory\BuilderiusCategoryFromTermFactory;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentCategoryTaxonomy;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;
use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDeleteSavedFragmentCategoryResolver  implements GraphQLFieldResolverInterface
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
        $input = $args['input'];
        $existingTerm = get_term_by('term_id', (int)$input['id'], BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        if (!$existingTerm) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'deleteCategory';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $termToBeDeleted = get_term_by('term_id', (int)$args['id'], BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        if (!$termToBeDeleted) {
            throw new \Exception('Invalid Builderius Category ID.', 400);
        }
        $existingCategory = BuilderiusCategoryFromTermFactory::createCategory($termToBeDeleted);
        $existingCategory->setGroups(['saved_fragment']);
        $this->eventDispatcher->dispatch(new ObjectContainingEvent($existingCategory), 'builderius_category_before_delete');

        $deletedTerm = wp_delete_term($termToBeDeleted->term_id, BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        if ($deletedTerm instanceof \WP_Error) {
            throw new \Exception($deletedTerm->get_error_message(), 400);
        }

        return new \ArrayObject(['result' => true, 'message' => 'Category was deleted successfully.']);
    }
}