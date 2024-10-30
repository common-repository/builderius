<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule;

class BuilderiusTemplateApplyRuleDecorator implements BuilderiusTemplateApplyRuleInterface
{
    /**
     * @var BuilderiusTemplateApplyRuleInterface
     */
    protected $applyRule;

    /**
     * @param BuilderiusTemplateApplyRuleInterface $applyRule
     */
    public function __construct(BuilderiusTemplateApplyRuleInterface $applyRule)
    {
        $this->applyRule = $applyRule;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateTypes()
    {
        return $this->applyRule->getTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setTemplateTypes(array $templateTypes)
    {
        $this->applyRule->setTemplateTypes($templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTemplateType($templateType)
    {
        $this->applyRule->addTemplateType($templateType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedTemplateTypes()
    {
        return $this->applyRule->getExcludedTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setExcludedTemplateTypes(array $templateTypes)
    {
        $this->applyRule->setExcludedTemplateTypes($templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedTemplateType($templateType)
    {
        $this->applyRule->addExcludedTemplateType($templateType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllTemplateTypes()
    {
        return $this->applyRule->isAppliedToAllTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTemplateTypes($appliedToAllTemplateTypes)
    {
        $this->applyRule->setAppliedToAllTemplateTypes($appliedToAllTemplateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->applyRule->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->applyRule->setName($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->applyRule->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->applyRule->setLabel($label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryName()
    {
        return $this->applyRule->getCategoryName();
    }

    /**
     * @inheritDoc
     */
    public function setCategoryName($categoryName)
    {
        $this->applyRule->setCategoryName($categoryName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->applyRule->getParent();
    }

    /**
     * @inheritDoc
     */
    public function setParent(BuilderiusTemplateApplyRuleInterface $parent)
    {
        $this->applyRule->setParent($parent);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        return $this->applyRule->getChildren();
    }

    /**
     * @inheritDoc
     */
    public function getChild($name)
    {
        return $this->applyRule->getChild($name);
    }

    /**
     * @inheritDoc
     */
    public function addChild(BuilderiusTemplateApplyRuleInterface $rule)
    {
        $this->applyRule->addChild($rule);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpression()
    {
        return $this->applyRule->getExpression();
    }

    /**
     * @inheritDoc
     */
    public function setExpression($expression)
    {
        $this->applyRule->setExpression($expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVariants()
    {
        return $this->applyRule->getVariants();
    }

    /**
     * @inheritDoc
     */
    public function getVariant($name)
    {
        return $this->applyRule->getVariant($name);
    }

    /**
     * @inheritDoc
     */
    public function addVariant(BuilderiusTemplateApplyRuleVariantInterface $variant)
    {
        $this->applyRule->addVariant($variant);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSelectAllAllowed()
    {
        return $this->applyRule->isSelectAllAllowed();
    }

    /**
     * @inheritDoc
     */
    public function setSelectAllAllowed($selectAllAllowed)
    {
        $this->applyRule->setSelectAllAllowed($selectAllAllowed);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSelectAllLabel()
    {
        return $this->applyRule->getSelectAllLabel();
    }

    /**
     * @inheritDoc
     */
    public function setSelectAllLabel($selectAllLabel)
    {
        $this->applyRule->setSelectAllLabel($selectAllLabel);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ignoreIfNoChildren()
    {
        return $this->applyRule->ignoreIfNoChildren();
    }

    /**
     * @inheritDoc
     */
    public function setIgnoreIfNoChildren($ignoreIfNoChildren)
    {
        $this->applyRule->setIgnoreIfNoChildren($ignoreIfNoChildren);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ignoreIfNoVariants()
    {
        return $this->applyRule->ignoreIfNoVariants();
    }

    /**
     * @inheritDoc
     */
    public function setIgnoreIfNoVariants($ignoreIfNoVariants)
    {
        $this->applyRule->setIgnoreIfNoVariants($ignoreIfNoVariants);

        return $this;
    }
}