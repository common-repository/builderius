<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateApplyRule extends ParameterBag implements
    BuilderiusTemplateApplyRuleInterface
{
    const TEMPLATE_TYPES_FIELD = 'templateTypes';
    const EXCLUDED_TEMPLATE_TYPES_FIELD = 'excludedTemplateTypes';
    const APPLIED_TO_ALL_TEMPLATE_TYPES_FIELD = 'appliedToAllTemplateTypes';
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const CATEGORY_FIELD = 'category';
    const PARENT_FIELD = 'parent';
    const CHILDREN_FIELD = 'children';
    const EXPRESSION_FIELD = 'expression';
    const VARIANTS_FIELD = 'variants';
    const SELECT_ALL_ALLOWED_FIELD = 'select_all';
    const SELECT_ALL_LABEL_FIELD = 'select_all_label';
    const IGNORE_IF_NO_CHILDREN_FIELD = 'ignore_if_no_children';
    const IGNORE_IF_NO_VARIANTS_FIELD = 'ignore_if_no_variants';

    /**
     * @inheritDoc
     */
    public function getTemplateTypes()
    {
        return $this->get(self::TEMPLATE_TYPES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTemplateTypes(array $templateTypes)
    {
        $this->set(self::TEMPLATE_TYPES_FIELD, $templateTypes);
        if (!empty($templateTypes)) {
            $this->setAppliedToAllTemplateTypes(false);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTemplateType($templateType)
    {
        $templateTypes = $this->getTemplateTypes();
        if (!in_array($templateType, $templateTypes)) {
            $templateTypes[] = $templateType;
            $this->setAppliedToAllTemplateTypes(false);
            $this->set(self::TEMPLATE_TYPES_FIELD, $templateTypes);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedTemplateTypes()
    {
        return $this->get(self::EXCLUDED_TEMPLATE_TYPES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setExcludedTemplateTypes(array $templateTypes)
    {
        $this->set(self::EXCLUDED_TEMPLATE_TYPES_FIELD, $templateTypes);
        if (!empty($templateTypes)) {
            $this->setAppliedToAllTemplateTypes(true);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedTemplateType($templateType)
    {
        $templateTypes = $this->getExcludedTemplateTypes();
        if (!in_array($templateType, $templateTypes)) {
            $templateTypes[] = $templateType;
            $this->setAppliedToAllTemplateTypes(true);
            $this->set(self::EXCLUDED_TEMPLATE_TYPES_FIELD, $templateTypes);
        }

        return $this;
    }
    /**
     * @inheritDoc
     */
    public function isAppliedToAllTemplateTypes()
    {
        return (bool)$this->get(self::APPLIED_TO_ALL_TEMPLATE_TYPES_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTemplateTypes($appliedToAllTemplateTypes)
    {
        $this->set(self::APPLIED_TO_ALL_TEMPLATE_TYPES_FIELD, $appliedToAllTemplateTypes);
        if (true === $appliedToAllTemplateTypes) {
            $this->setTemplateTypes([]);
        } else {
            $this->setExcludedTemplateTypes([]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return __($this->get(self::LABEL_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryName()
    {
        return $this->get(self::CATEGORY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryName($categoryName)
    {
        $this->set(self::CATEGORY_FIELD, $categoryName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->get(self::PARENT_FIELD);
    }

    /**
     * @param BuilderiusTemplateApplyRuleInterface $parent
     * @return $this
     */
    public function setParent(BuilderiusTemplateApplyRuleInterface $parent)
    {
        $this->set(self::PARENT_FIELD, $parent);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        return $this->get(self::CHILDREN_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function getChild($name)
    {
        return isset($this->getChildren()[$name]) ? $this->getChildren()[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function addChild(BuilderiusTemplateApplyRuleInterface $rule)
    {
        $children = $this->getChildren();
        $children[$rule->getName()] = $rule;
        $this->set(self::CHILDREN_FIELD, $children);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpression()
    {
        return $this->get(self::EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setExpression($expression)
    {
        $this->set(self::EXPRESSION_FIELD, $expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVariants()
    {
        return $this->get(self::VARIANTS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function getVariant($name)
    {
        if (isset($this->getVariants()[$name])) {
            return $this->getVariants()[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function addVariant(BuilderiusTemplateApplyRuleVariantInterface $variant)
    {
        if ($variant->getRule() !== $this) {
            $variant->setRule($this);
        }
        $variants = $this->getVariants();
        $variants[$variant->getName()] = $variant;
        $this->set(self::VARIANTS_FIELD, $variants);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSelectAllAllowed()
    {
        return (bool)$this->get(self::SELECT_ALL_ALLOWED_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setSelectAllAllowed($selectAllAllowed)
    {
        $this->set(self::SELECT_ALL_ALLOWED_FIELD, $selectAllAllowed);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSelectAllLabel()
    {
        return $this->get(self::SELECT_ALL_LABEL_FIELD, 'All');
    }

    /**
     * @inheritDoc
     */
    public function setSelectAllLabel($selectAllLabel)
    {
        $this->set(self::SELECT_ALL_LABEL_FIELD, $selectAllLabel);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ignoreIfNoChildren()
    {
        return (bool)$this->get(self::IGNORE_IF_NO_CHILDREN_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setIgnoreIfNoChildren($ignoreIfNoChildren)
    {
        $this->set(self::IGNORE_IF_NO_CHILDREN_FIELD, $ignoreIfNoChildren);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ignoreIfNoVariants()
    {
        return (bool)$this->get(self::IGNORE_IF_NO_VARIANTS_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setIgnoreIfNoVariants($ignoreIfNoVariants)
    {
        $this->set(self::IGNORE_IF_NO_VARIANTS_FIELD, $ignoreIfNoVariants);

        return $this;
    }
}
