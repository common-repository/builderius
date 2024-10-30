<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

class DynamicDataHelpersCategoriesProvider implements DynamicDataHelpersCategoriesProviderInterface
{
    /**
     * @var DynamicDataHelpersCategoryInterface[]
     */
    private $categories = [];

    /**
     * @param DynamicDataHelpersCategoryInterface $category
     * @return $this
     */
    public function addCategory(DynamicDataHelpersCategoryInterface $category)
    {
        $this->categories[$category->getName()] = $category;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @inheritDoc
     */
    public function getCategory($name)
    {
        if ($this->hasCategory($name)) {
            return $this->categories[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasCategory($name)
    {
        return isset($this->categories[$name]);
    }
}