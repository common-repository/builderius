<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

interface DynamicDataHelpersCategoriesProviderInterface
{
    /**
     * @return DynamicDataHelpersCategoryInterface[]
     */
    public function getCategories();

    /**
     * @return DynamicDataHelpersCategoryInterface|null
     */
    public function getCategory($name);

    /**
     * @return bool
     */
    public function hasCategory($name);
}