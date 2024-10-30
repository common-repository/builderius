<?php

namespace Builderius\Bundle\SavedFragmentBundle\GraphQL\Resolver;

use Builderius\Bundle\CategoryBundle\Factory\BuilderiusCategoryFromTermFactory;
use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategory;
use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategoryInterface;
use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentCategoryTaxonomy;
use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldUpdateSavedFragmentCategoryResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusCategoriesProviderInterface
     */
    private $categoriesProvider;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusCategoriesProviderInterface $categoriesProvider
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusCategoriesProviderInterface $categoriesProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->categoriesProvider = $categoriesProvider;
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
        return 'updateCategory';
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
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        $existingTerm = get_term_by('term_id', (int)$input['id'], BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        if (!$existingTerm) {
            throw new \Exception('Invalid Category ID.', 400);
        }
        $existingCategory = BuilderiusCategoryFromTermFactory::createCategory($existingTerm);
        $existingCategory->setGroups(['saved_fragment']);
        if (array_key_exists(BuilderiusCategory::NAME_FIELD, $input)) {
            $existingCategorySameName = $this->categoriesProvider->getCategory('saved_fragment', $input[BuilderiusCategory::NAME_FIELD]);
            if ($existingCategorySameName) {
                throw new \Exception('Category with same name and group already exists.', 400);
            }
        }
        $existingCategories = $this->categoriesProvider->getCategories('saved_fragment');
        foreach ($existingCategories as $existingCategory) {
            if ($existingCategory->getLabel(false) === $input[BuilderiusCategory::LABEL_FIELD]) {
                throw new \Exception('Category with same label and group already exists.', 400);
            }
        }

        if (array_key_exists(BuilderiusCategory::NAME_FIELD, $input)) {
            $existingCategory->setName($input[BuilderiusCategory::NAME_FIELD]);
        }
        if (array_key_exists(BuilderiusCategory::LABEL_FIELD, $input)) {
            $existingCategory->setLabel($input[BuilderiusCategory::LABEL_FIELD]);
        }
        if (array_key_exists(BuilderiusCategory::SORT_ORDER_FIELD, $input)) {
            $existingCategory->setSortOrder($input[BuilderiusCategory::SORT_ORDER_FIELD]);
        }
        if (array_key_exists(BuilderiusCategory::DEFAULT_FIELD, $input)) {
            $existingCategory->setDefault($input[BuilderiusCategory::DEFAULT_FIELD]);
        }
        $event = new ObjectContainingEvent($existingCategory);
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_category_before_update'
        );

        if ($event->getError() && is_wp_error($event->getError())) {
            throw new \Exception($event->getError()->get_error_message(), 400);
        }
        /** @var BuilderiusCategoryInterface $category */
        $category = $event->getObject();
        $termResult = wp_update_term(
            $category->getId(),
            BuilderiusSavedFragmentCategoryTaxonomy::NAME,
            ['name' => $category->getName()]
        );
        if ($termResult instanceof \WP_Error) {
            throw new \Exception($termResult->get_error_messages(), 400);
        }
        $term = get_term_by('term_id', $termResult['term_id'], BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        update_term_meta($term->term_id, BuilderiusCategory::LABEL_FIELD, $category->getLabel());
        update_term_meta($term->term_id, BuilderiusCategory::SORT_ORDER_FIELD, $category->getSortOrder());
        update_term_meta($term->term_id, BuilderiusCategory::DEFAULT_FIELD, $category->isDefault());
        $category = BuilderiusCategoryFromTermFactory::createCategory($term);
        $category->setGroups(['saved_fragment']);

        $this->eventDispatcher->dispatch(new ObjectContainingEvent($category), 'builderius_category_updated');

        return new \ArrayObject(['category' => $category]);
    }
}