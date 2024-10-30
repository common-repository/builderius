<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingValueExpressionInterface
{
    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);
    
    /**
     * @return string
     */
    public function getName();

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
     * @param string $formatExpression
     * @return $this
     */
    public function setFormatExpression($formatExpression);
    
    /**
     * @return string
     */
    public function getFormatExpression();

    /**
     * @param string $contextSource
     * @return $this
     */
    public function setContextSource($contextSource);

    /**
     * @return string
     */
    public function getContextSource();

    /**
     * @param BuilderiusSettingValueExpressionInterface $expression
     * @return $this
     */
    public function addDependsOnSettingValueExpression(BuilderiusSettingValueExpressionInterface $expression);

    /**
     * @param BuilderiusSettingValueExpressionsCollectionInterface $expressions
     * @return $this
     */
    public function setDependsOnSettingValueExpressions(BuilderiusSettingValueExpressionsCollectionInterface $expressions);

    /**
     * @return BuilderiusSettingValueExpressionInterface[]
     */
    public function getDependsOnSettingValueExpressions();
}
