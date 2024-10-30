<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingCssAtRuleInterface
{
    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier);
    
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $conditionExpression
     * @return $this
     */
    public function setConditionExpression($conditionExpression);
    
    /**
     * @return string
     */
    public function getConditionExpression();

    /**
     * @param string $ruleExpression
     * @return $this
     */
    public function setRuleExpression($ruleExpression);
    
    /**
     * @return string
     */
    public function getRuleExpression();
}
