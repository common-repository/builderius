<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry;

use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplyRuleCategoryInterface;

class BuilderiusTemplateApplyRuleCategoriesRegistry implements
    BuilderiusTemplateApplyRuleCategoriesRegistryInterface
{
    /**
     * @var BuilderiusTemplateApplyRuleCategoryInterface[]
     */
    private $categories = [];

    /**
     * @param BuilderiusTemplateApplyRuleCategoryInterface $category
     * @return $this
     */
    public function addCategory(BuilderiusTemplateApplyRuleCategoryInterface $category)
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
        
        throw new \Exception(sprintf('There is no "%s" category registered in %s', $name, self::class));
    }

    /**
     * @inheritDoc
     */
    public function hasCategory($name)
    {
        return isset($this->categories[$name]);
    }
}
