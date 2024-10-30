<?php

namespace Builderius\Bundle\CategoryBundle\GraphQL\Resolver;

use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Bundle\SavedFragmentBundle\Factory\BuilderiusSavedFragmentFromPostFactory;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragment;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;

class BuilderiusRootQueryFieldCategoryResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var BuilderiusCategoriesProviderInterface
     */
    private $categoriesProvider;

    /**
     * @param BuilderiusCategoriesProviderInterface $categoriesProvider
     */
    public function __construct(BuilderiusCategoriesProviderInterface $categoriesProvider)
    {
        $this->categoriesProvider = $categoriesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootQuery'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'category';
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
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $category = $this->categoriesProvider->getCategory($args['group'], $args['name']);
        if (!$category) {
            throw new \Exception('There is no Builderius Category with provided name and group', 400);
        }

        return $category;
    }
}