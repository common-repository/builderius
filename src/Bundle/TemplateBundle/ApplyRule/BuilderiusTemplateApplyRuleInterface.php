<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule;

interface BuilderiusTemplateApplyRuleInterface
{
    /**
     * @return array
     */
    public function getTemplateTypes();

    /**
     * @param array $templateTypes
     * @return $this
     */
    public function setTemplateTypes(array $templateTypes);

    /**
     * @param string $templateType
     * @return $this
     */
    public function addTemplateType($templateType);
    /**
     * @return array
     */
    public function getExcludedTemplateTypes();

    /**
     * @param array $templateTypes
     * @return $this
     */
    public function setExcludedTemplateTypes(array $templateTypes);

    /**
     * @param string $templateType
     * @return $this
     */
    public function addExcludedTemplateType($templateType);

    /**
     * @return bool
     */
    public function isAppliedToAllTemplateTypes();

    /**
     * @param bool $appliedToAllTemplateTypes
     * @return $this
     */
    public function setAppliedToAllTemplateTypes($appliedToAllTemplateTypes);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getCategoryName();

    /**
     * @param string $categoryName
     * @return $this
     */
    public function setCategoryName($categoryName);

    /**
     * @return BuilderiusTemplateApplyRuleInterface|null
     */
    public function getParent();
    
    /**
     * @param BuilderiusTemplateApplyRuleInterface $parent
     * @return $this
     */
    public function setParent(BuilderiusTemplateApplyRuleInterface $parent);

    /**
     * @return BuilderiusTemplateApplyRuleInterface[]
     */
    public function getChildren();
    
    /**
     * @param string $name
     * @return BuilderiusTemplateApplyRuleInterface
     */
    public function getChild($name);

    /**
     * @param BuilderiusTemplateApplyRuleInterface $rule
     * @return $this
     */
    public function addChild(BuilderiusTemplateApplyRuleInterface $rule);

    /**
     * @return string
     */
    public function getExpression();

    /**
     * @param string $expression
     * @return $this
     */
    public function setExpression($expression);

    /**
     * @return BuilderiusTemplateApplyRuleVariantInterface[]
     */
    public function getVariants();
    
    /**
     * @param string $name
     * @return BuilderiusTemplateApplyRuleVariantInterface
     */
    public function getVariant($name);
    
    /**
     * @param BuilderiusTemplateApplyRuleVariantInterface $variant
     * @return $this
     */
    public function addVariant(BuilderiusTemplateApplyRuleVariantInterface $variant);
    
    /**
     * @return bool
     */
    public function isSelectAllAllowed();

    /**
     * @param bool $selectAllAllowed
     * @return $this
     */
    public function setSelectAllAllowed($selectAllAllowed);

    /**
     * @return string
     */
    public function getSelectAllLabel();

    /**
     * @param string $selectAllLabel
     * @return $this
     */
    public function setSelectAllLabel($selectAllLabel);

    /**
     * @return bool
     */
    public function ignoreIfNoChildren();

    /**
     * @param bool $ignoreIfNoChildren
     * @return $this
     */
    public function setIgnoreIfNoChildren($ignoreIfNoChildren);

    /**
     * @return bool
     */
    public function ignoreIfNoVariants();

    /**
     * @param bool $ignoreIfNoVariants
     * @return $this
     */
    public function setIgnoreIfNoVariants($ignoreIfNoVariants);
}
