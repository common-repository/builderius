<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSettingCssAtRule extends ParameterBag implements BuilderiusSettingCssAtRuleInterface
{
    const IDENTIFIER_FIELD = 'identifier';
    const CONDITION_EXPRESSION_FIELD = 'conditionExpression';
    const RULE_EXPRESSION_FIELD = 'ruleExpression';

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        $this->set(self::IDENTIFIER_FIELD, $identifier);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->get(self::IDENTIFIER_FIELD);
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
    public function setRuleExpression($ruleExpression)
    {
        $this->set(self::RULE_EXPRESSION_FIELD, $ruleExpression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRuleExpression()
    {
        return $this->get(self::RULE_EXPRESSION_FIELD);
    }
}
