<?php

namespace Builderius\Bundle\CategoryBundle\GraphQL\Resolver;

use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldCategoriesResolver implements GraphQLFieldResolverInterface
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
        return 'categories';
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
        return $this->categoriesProvider->getCategories(array_key_exists('group', $args) ? $args['group'] : null);
    }
}