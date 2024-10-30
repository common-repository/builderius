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

class BuilderiusRootMutationFieldCreateSavedFragmentCategoryResolver implements GraphQLFieldResolverInterface
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
        return 'createCategory';
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (!isset($args['input']['groups'])) {
            throw new \Exception('Missing required argument "groups".', 400);
        }

        return in_array('saved_fragment', $args['input']['groups']) ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        if (array_key_exists(BuilderiusCategory::NAME_FIELD, $input)) {
            $existingCategory = $this->categoriesProvider->getCategory('saved_fragment', $input[BuilderiusCategory::NAME_FIELD]);
            if ($existingCategory) {
                throw new \Exception('Category with same name and group already exists.', 400);
            }
        }
        $existingCategories = $this->categoriesProvider->getCategories('saved_fragment');
        foreach ($existingCategories as $existingCategory) {
            if ($existingCategory->getLabel(false) === $input[BuilderiusCategory::LABEL_FIELD]) {
                throw new \Exception('Category with same label and group already exists.', 400);
            }
        }

        $category = new BuilderiusCategory([
            BuilderiusCategory::NAME_FIELD => array_key_exists(BuilderiusCategory::NAME_FIELD, $input) ? $input[BuilderiusCategory::NAME_FIELD] : $input[BuilderiusCategory::LABEL_FIELD],
            BuilderiusCategory::LABEL_FIELD => $input[BuilderiusCategory::LABEL_FIELD],
            BuilderiusCategory::SORT_ORDER_FIELD => array_key_exists(BuilderiusCategory::SORT_ORDER_FIELD, $input) ? $input[BuilderiusCategory::SORT_ORDER_FIELD] : 10,
            BuilderiusCategory::GROUPS_FIELD => $input[BuilderiusCategory::GROUPS_FIELD],
            BuilderiusCategory::EDITABLE_FIELD => true,
            BuilderiusCategory::DEFAULT_FIELD => array_key_exists(BuilderiusCategory::DEFAULT_FIELD, $input) ? $input[BuilderiusCategory::DEFAULT_FIELD] : false,
        ]);
        $event = new ObjectContainingEvent($category);
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_category_before_create'
        );

        if ($event->getError() && is_wp_error($event->getError())) {
            throw new \Exception($event->getError()->get_error_message(), 400);
        }
        /** @var BuilderiusCategoryInterface $category */
        $category = $event->getObject();
        $termResult = wp_insert_term($category->getName(), BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        if ($termResult instanceof \WP_Error) {
            throw new \Exception($termResult->get_error_messages(), 400);
        }
        $term = get_term_by('term_id', $termResult['term_id'], BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        add_term_meta($term->term_id, BuilderiusCategory::LABEL_FIELD, $category->getLabel(), true);
        add_term_meta($term->term_id, BuilderiusCategory::SORT_ORDER_FIELD, $category->getSortOrder(), true);
        add_term_meta($term->term_id, BuilderiusCategory::DEFAULT_FIELD, $category->isDefault(), true);
        $category = BuilderiusCategoryFromTermFactory::createCategory($term);
        $category->setGroups(['saved_fragment']);

        $this->eventDispatcher->dispatch(new ObjectContainingEvent($category), 'builderius_category_created');

        return new \ArrayObject(['category' => $category]);
    }
}