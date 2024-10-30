<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

interface BuilderiusTemplateApplicantCategoriesProviderInterface
{
    /**
     * @return BuilderiusTemplateApplicantCategoryInterface[]
     */
    public function getCategories();

    /**
     * @return BuilderiusTemplateApplicantCategoryInterface|null
     */
    public function getCategory($name);

    /**
     * @return bool
     */
    public function hasCategory($name);
}