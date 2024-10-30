<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Registry;

use Builderius\Bundle\TemplateBundle\ApplyRule\BuilderiusTemplateApplyRuleInterface;

class BuilderiusTemplateApplyRulesRegistry implements BuilderiusTemplateApplyRulesRegistryInterface
{
    /**
     * @var BuilderiusTemplateApplyRuleInterface[]
     */
    private $rules = [];

    /**
     * @param BuilderiusTemplateApplyRuleInterface $rule
     */
    public function addRule(BuilderiusTemplateApplyRuleInterface $rule)
    {
        $this->rules[$rule->getCategoryName()][$rule->getName()] = $rule;
    }

    /**
     * @inheritDoc
     */
    public function getRules()
    {
        $rules = [];
        foreach ($this->rules as $category => $rulesByNames) {
            /** @var BuilderiusTemplateApplyRuleInterface $rule */
            foreach ($rulesByNames as $name => $rule) {
                $rules[$category][$name] = $this->getRule($name, $category);
            }
        }

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryRules($category)
    {
        $rules = [];
        foreach ($this->rules[$category] as $rulesByNames) {
            /** @var BuilderiusTemplateApplyRuleInterface $rule */
            foreach ($rulesByNames as $name => $rule) {
                $rules[$name] = $this->getRule($name, $category);
            }
        }

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function getRule($name, $category)
    {
        if ($this->hasRule($name, $category)) {
            /** @var BuilderiusTemplateApplyRuleInterface $rule */
            $rule = $this->rules[$category][$name];
            $parent = $rule->getParent();
            if (is_string($parent) && $this->hasRule($parent, $category)) {
                $rule->setParent($this->getRule($parent, $category));
            }
            return $rule;
        }
        
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasRule($name, $category)
    {
        return isset($this->rules[$category][$name]);
    }
}
