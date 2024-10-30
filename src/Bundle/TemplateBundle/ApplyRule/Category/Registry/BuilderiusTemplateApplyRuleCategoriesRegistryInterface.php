<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry;

use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplyRuleCategoryInterface;

interface BuilderiusTemplateApplyRuleCategoriesRegistryInterface
{
    /**
     * @return BuilderiusTemplateApplyRuleCategoryInterface[]
     */
    public function getCategories();

    /**
     * @param string $name
     * @return BuilderiusTemplateApplyRuleCategoryInterface
     * @throws \Exception
     */
    public function getCategory($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasCategory($name);
}
