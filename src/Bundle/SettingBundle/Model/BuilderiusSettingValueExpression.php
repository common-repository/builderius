<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSettingValueExpression extends ParameterBag implements BuilderiusSettingValueExpressionInterface
{
    const NAME_FIELD = 'name';
    const CONDITION_EXPRESSION_FIELD = 'conditionExpression';
    const FORMAT_EXPRESSION_FIELD = 'formatExpression';
    const CONTEXT_SOURCE_FIELD = 'contextSource';
    const DEPENDS_ON_EXPRESSIONS = 'dependsOnExpressions';

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
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setConditionExpression($conditionExpression)
    {
        $this->set(self::CONDITION_EXPRESSION_FIELD, $conditionExpression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConditionExpression()
    {
        return $this->get(self::CONDITION_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setFormatExpression($formatExpression)
    {
        $this->set(self::FORMAT_EXPRESSION_FIELD, $formatExpression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFormatExpression()
    {
        return $this->get(self::FORMAT_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setContextSource($contextSource)
    {
        $this->set(self::CONTEXT_SOURCE_FIELD, $contextSource);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContextSource()
    {
        return $this->get(self::CONTEXT_SOURCE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function addDependsOnSettingValueExpression(BuilderiusSettingValueExpressionInterface $expression)
    {
        $expressions = $this->get(self::DEPENDS_ON_EXPRESSIONS, []);
        if (!in_array($expression, $expressions)) {
            $expressions[] = $expression;
            $this->set(self::DEPENDS_ON_EXPRESSIONS, $expressions);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDependsOnSettingValueExpressions(BuilderiusSettingValueExpressionsCollectionInterface $expressions)
    {
        $this->set(self::DEPENDS_ON_EXPRESSIONS, []);
        foreach ($expressions->toArray() as $expression) {
            $this->addDependsOnSettingValueExpression($expression);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDependsOnSettingValueExpressions()
    {
        return $this->get(self::DEPENDS_ON_EXPRESSIONS, []);
    }
}
