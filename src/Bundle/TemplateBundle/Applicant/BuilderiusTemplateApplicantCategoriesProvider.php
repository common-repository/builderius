<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

class BuilderiusTemplateApplicantCategoriesProvider implements BuilderiusTemplateApplicantCategoriesProviderInterface
{
    /**
     * @var BuilderiusTemplateApplicantCategoryInterface[]
     */
    private $categories = [];

    /**
     * @param BuilderiusTemplateApplicantCategoryInterface $category
     * @return $this
     */
    public function addCategory(BuilderiusTemplateApplicantCategoryInterface $category)
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