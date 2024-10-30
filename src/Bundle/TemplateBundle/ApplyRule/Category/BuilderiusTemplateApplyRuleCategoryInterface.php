<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Category;

interface BuilderiusTemplateApplyRuleCategoryInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @return string
     */
    public function getVariableAlias();
    
    /**
     * @return object|null
     */
    public function getVariableObject();
}
