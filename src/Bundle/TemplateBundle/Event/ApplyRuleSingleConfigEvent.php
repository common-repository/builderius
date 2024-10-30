<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplyRuleCategoryInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class ApplyRuleSingleConfigEvent extends Event
{
    /**
     * @var BuilderiusTemplateApplyRuleCategoryInterface
     */
    private $category;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var bool
     */
    private $expressionResult;

    /**
     * @var array
     */
    private $applyRulesConfig;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var mixed
     */
    private $argument;

    /**
     * @var string
     */
    private $operator;

    /**
     * @param BuilderiusTemplateApplyRuleCategoryInterface $category
     * @param string $expression
     * @param bool $expressionResult
     * @param array $applyRulesConfig
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     */
    public function __construct(
        BuilderiusTemplateApplyRuleCategoryInterface $category,
        $expression,
        $expressionResult,
        array $applyRulesConfig,
        $rule,
        $argument,
        $operator
    ) {
        $this->category = $category;
        $this->expression = $expression;
        $this->expressionResult = $expressionResult;
        $this->applyRulesConfig = $applyRulesConfig;
        $this->rule = $rule;
        $this->argument = $argument;
        $this->operator = $operator;
    }

    /**
     * @return BuilderiusTemplateApplyRuleCategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param BuilderiusTemplateApplyRuleCategoryInterface $category
     * @return $this
     */
    public function setCategory(BuilderiusTemplateApplyRuleCategoryInterface $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return bool
     */
    public function getExpressionResult()
    {
        return $this->expressionResult;
    }

    /**
     * @param bool $expressionResult
     * @return $this
     */
    public function setExpressionResult($expressionResult)
    {
        $this->expressionResult = $expressionResult;

        return $this;
    }

    /**
     * @return array
     */
    public function getApplyRulesConfig()
    {
        return $this->applyRulesConfig;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return mixed
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}