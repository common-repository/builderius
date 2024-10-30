<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Category;

class BuilderiusTemplateApplyRuleThemeCategory extends AbstractBuilderiusTemplateApplyRuleCategory implements
    BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface
{
    const NAME = 'theme';
    const ALIAS ='query';
    const SORT_ORDER = 10;

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return __('Template Hierarchy');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return __('The goal of apply rules in this category is to make possible to apply the template based on Wordpress template hierarchy which is described in details here: https://wphierarchy.com/. Apply rules of this category are mandatory, so at least one rule should be created.', 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function getVariableObject()
    {
        global $wp_query;

        return $wp_query;
    }
}
