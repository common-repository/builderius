<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\ApplyRuleArgumentsProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateApplyRuleVariant extends ParameterBag implements
    BuilderiusTemplateApplyRuleVariantInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const RULE_FIELD = 'rule';
    const EXPRESSION_FIELD = 'expression';
    const ARGUMENT_FIELD = 'argument';
    const POSSIBLE_ARGUMENTS_PROVIDER_FIELD = 'possible_arguments_provider';
    const POSSIBLE_ARGUMENTS_FIELD = 'possible_arguments';
    const SELECT_ALL_ALLOWED_FIELD = 'select_all';
    const OPERATORS_FIELD = 'operators';
    const WIDGET_TYPE = 'widget_type';
    const PLACEHOLDER_FIELD = 'placeholder';

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
    public function getRule()
    {
        return $this->get(self::RULE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setRule($rule)
    {
        $this->set(self::RULE_FIELD, $rule);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpression()
    {
        $expression = $this->get(self::EXPRESSION_FIELD);
        if ($this->getArgument() && strpos($expression, 'argument') !== false) {
            return str_replace('argument', $this->getArgument(), $expression);
        }
        return $expression;
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
    public function getArgument()
    {
        return $this->get(self::ARGUMENT_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setArgument($argument)
    {
        $this->set(self::ARGUMENT_FIELD, $argument);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPossibleArguments()
    {
        if ($possbleArguments = $this->get(self::POSSIBLE_ARGUMENTS_FIELD)) {
            return $possbleArguments;
        } elseif ($possibleArgumentsProvider = $this->get(self::POSSIBLE_ARGUMENTS_PROVIDER_FIELD)) {
            return $possibleArgumentsProvider->getArguments();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setPossibleArguments(array $possibleArguments)
    {
        $this->set(self::POSSIBLE_ARGUMENTS_FIELD, $possibleArguments);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPossibleArgumentsProvider(ApplyRuleArgumentsProviderInterface $possibleArgumentsProvider)
    {
        $this->set(self::POSSIBLE_ARGUMENTS_PROVIDER_FIELD, $possibleArgumentsProvider);

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
    public function setSelectAllAllowed($allAllowed = false)
    {
        $this->set(self::SELECT_ALL_ALLOWED_FIELD, $allAllowed);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return $this->get(self::OPERATORS_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setOperators(array $operators)
    {
        $this->set(self::OPERATORS_FIELD, $operators);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWidgetType()
    {
        return $this->get(self::WIDGET_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setWidgetType($widgetType)
    {
        $this->set(self::WIDGET_TYPE, $widgetType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPlaceholder()
    {
        return $this->get(self::PLACEHOLDER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setPlaceholder($placeholder)
    {
        $this->set(self::PLACEHOLDER_FIELD, $placeholder);

        return $this;
    }
}
