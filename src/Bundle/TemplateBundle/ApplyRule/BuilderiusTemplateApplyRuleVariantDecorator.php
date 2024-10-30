<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\ApplyRuleArgumentsProviderInterface;

class BuilderiusTemplateApplyRuleVariantDecorator implements BuilderiusTemplateApplyRuleVariantInterface
{
    /**
     * @var BuilderiusTemplateApplyRuleVariantInterface
     */
    protected $applyRuleVariant;

    /**
     * @param BuilderiusTemplateApplyRuleVariantInterface $applyRuleVariant
     */
    public function __construct(BuilderiusTemplateApplyRuleVariantInterface $applyRuleVariant)
    {
        $this->applyRuleVariant = $applyRuleVariant;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->applyRuleVariant->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->applyRuleVariant->setName($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->applyRuleVariant->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->applyRuleVariant->setLabel($label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRule()
    {
        return $this->applyRuleVariant->getRule();
    }

    /**
     * @inheritDoc
     */
    public function setRule($rule)
    {
        $this->applyRuleVariant->setRule($rule);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpression()
    {
        return $this->applyRuleVariant->getExpression();
    }

    /**
     * @inheritDoc
     */
    public function setExpression($expression)
    {
        $this->applyRuleVariant->setExpression($expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getArgument()
    {
        return $this->applyRuleVariant->getArgument();
    }

    /**
     * @inheritDoc
     */
    public function setArgument($argument)
    {
        $this->applyRuleVariant->setArgument($argument);
    }

    /**
     * @inheritDoc
     */
    public function getPossibleArguments()
    {
        return $this->applyRuleVariant->getPossibleArguments();
    }

    /**
     * @inheritDoc
     */
    public function setPossibleArguments(array $possibleArguments)
    {
        $this->applyRuleVariant->setPossibleArguments($possibleArguments);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPossibleArgumentsProvider(ApplyRuleArgumentsProviderInterface $possibleArgumentsProvider)
    {
        $this->applyRuleVariant->setPossibleArgumentsProvider($possibleArgumentsProvider);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSelectAllAllowed()
    {
        return $this->applyRuleVariant->isSelectAllAllowed();
    }

    /**
     * @inheritDoc
     */
    public function setSelectAllAllowed($allAllowed = false)
    {
        $this->applyRuleVariant->setSelectAllAllowed($allAllowed);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return $this->applyRuleVariant->getOperators();
    }

    /**
     * @inheritDoc
     */
    public function setOperators(array $operators)
    {
        $this->applyRuleVariant->setOperators($operators);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWidgetType()
    {
        return $this->applyRuleVariant->getWidgetType();
    }

    /**
     * @inheritDoc
     */
    public function setWidgetType($widgetType)
    {
        $this->applyRuleVariant->setWidgetType($widgetType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPlaceholder()
    {
        return $this->applyRuleVariant->getPlaceholder();
    }

    /**
     * @inheritDoc
     */
    public function setPlaceholder($placeholder)
    {
        $this->applyRuleVariant->setPlaceholder($placeholder);

        return $this;
    }
}