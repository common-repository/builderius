<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Category;

abstract class AbstractBuilderiusTemplateApplyRuleCategory implements BuilderiusTemplateApplyRuleCategoryInterface
{
    const NAME = null;
    const LABEL = null;
    const DESCRIPTION = null;
    const ALIAS = null;
    const SORT_ORDER = null;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return static::LABEL;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return static::DESCRIPTION;
    }
    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return static::SORT_ORDER;
    }

    /**
     * @inheritDoc
     */
    public function getVariableAlias()
    {
        return static::ALIAS;
    }
}
