<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Registry;

use Builderius\Bundle\TemplateBundle\ApplyRule\BuilderiusTemplateApplyRuleInterface;

interface BuilderiusTemplateApplyRulesRegistryInterface
{
    /**
     * @return BuilderiusTemplateApplyRuleInterface[]
     */
    public function getRules();

    /**
     * @param string $category
     * @return array
     */
    public function getCategoryRules($category);

    /**
     * @param string $name
     * @param string $category
     * @return BuilderiusTemplateApplyRuleInterface
     */
    public function getRule($name, $category);
    
    /**
     * @param string $name
     * @param string $category
     * @return bool
     */
    public function hasRule($name, $category);
}
