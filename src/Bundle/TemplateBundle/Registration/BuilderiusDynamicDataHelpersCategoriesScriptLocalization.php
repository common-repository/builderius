<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersCategoriesProviderInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersCategory;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusDynamicDataHelpersCategoriesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'dynamicDataHelpersCategories';

    /**
     * @var DynamicDataHelpersCategoriesProviderInterface
     */
    private $dynamicDataHelpersCategoriesProvider;

    /**
     * @param DynamicDataHelpersCategoriesProviderInterface $dynamicDataHelpersCategoriesProvider
     */
    public function __construct(
        DynamicDataHelpersCategoriesProviderInterface $dynamicDataHelpersCategoriesProvider
    ) {
        $this->dynamicDataHelpersCategoriesProvider = $dynamicDataHelpersCategoriesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->dynamicDataHelpersCategoriesProvider->getCategories() as $category) {
            $data[$category->getName()] = [
                DynamicDataHelpersCategory::LABEL_FIELD => __($category->getLabel()),
                DynamicDataHelpersCategory::SORT_ORDER_FIELD => $category->getSortOrder()
            ];
        }

        return $data;
    }
}
