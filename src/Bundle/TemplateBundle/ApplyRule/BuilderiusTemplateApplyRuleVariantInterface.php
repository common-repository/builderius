<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\ApplyRuleArgumentsProviderInterface;

interface BuilderiusTemplateApplyRuleVariantInterface
{
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
    public function getRule();

    /**
     * @param string $rule
     * @return $this
     */
    public function setRule($rule);

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
     * @return string
     */
    public function getArgument();

    /**
     * @param string $argument
     * @return $this
     */
    public function setArgument($argument);

    /**
     * @return array|null
     */
    public function getPossibleArguments();

    /**
     * @param array $possibleArguments
     * @return $this
     */
    public function setPossibleArguments(array $possibleArguments);

    /**
     * @param ApplyRuleArgumentsProviderInterface $possibleArgumentsProvider
     * @return $this
     */
    public function setPossibleArgumentsProvider(ApplyRuleArgumentsProviderInterface $possibleArgumentsProvider);

    /**
     * @return bool
     */
    public function isSelectAllAllowed();

    /**
     * @param bool $allAllowed
     * @return $this
     */
    public function setSelectAllAllowed($allAllowed = false);

    /**
     * @return array|null
     */
    public function getOperators();

    /**
     * @param array $operators
     * @return $this
     */
    public function setOperators(array $operators);

    /**
     * @return string|null
     */
    public function getWidgetType();

    /**
     * @param string $widgetType
     * @return $this
     */
    public function setWidgetType($widgetType);

    /**
     * @return string|null
     */
    public function getPlaceholder();

    /**
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder);
}
