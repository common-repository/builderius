<?php

namespace Builderius\Bundle\CategoryBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategory;
use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;

class BuilderiusCategoriesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'categories';

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
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->categoriesProvider->getCategories() as $group => $categories) {
            foreach ($categories as $category) {
                $name = $category->getName();
                $data[$group][$name] = [
                    BuilderiusCategory::ID_FIELD => $category->getId(),
                    BuilderiusCategory::NAME_FIELD => $name,
                    BuilderiusCategory::LABEL_FIELD => $category->getLabel(),
                    BuilderiusCategory::SORT_ORDER_FIELD => (int)$category->getSortOrder(),
                    BuilderiusCategory::EDITABLE_FIELD => $category->isEditable(),
                    BuilderiusCategory::DEFAULT_FIELD => $category->isDefault(),
                ];
            }
        }

        return $data;
    }
}
