<?php

namespace Builderius\Bundle\SettingBundle\Generator\FinalSettingValue;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FinalSettingValueGenerator implements FinalSettingValueGeneratorInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @inheritDoc
     */
    public function generateFinalSettingValue(
        BuilderiusSettingValueInterface $value,
        BuilderiusSettingValueExpressionInterface $valueExpression,
        array $settingSchema
    ) {
        $value = clone $value;
        if (!empty($valueExpression->getDependsOnSettingValueExpressions())) {
            $arrayValue = $value->getValue();
            foreach ($valueExpression->getDependsOnSettingValueExpressions() as $depExpression) {
                $generatedValue = $this->generateFinalSettingValue(
                    $value,
                    $depExpression,
                    $settingSchema
                );
                if (!isset($arrayValue[$depExpression->getName()]) || $arrayValue[$depExpression->getName()] === null) {
                    $arrayValue[$depExpression->getName()] = $generatedValue;
                } elseif (is_array($arrayValue[$depExpression->getName()]) && is_array($generatedValue)) {
                    foreach ($generatedValue as $k => $generatedValueItem) {
                        if (!isset($arrayValue[$depExpression->getName()][$k])) {
                            $arrayValue[$depExpression->getName()][$k] = $generatedValueItem;
                        } else {
                            $arrayValue[$depExpression->getName()][] = $generatedValueItem;
                        }
                    }
                    ksort($arrayValue[$depExpression->getName()]);
                }
            }
            $value->setValue($arrayValue);
        }
        $context = $value->getValue();
        $contextSource = $valueExpression->getContextSource();
        if ($contextSource && array_key_exists($contextSource, $context)) {
            $context = $context[$contextSource];
        }
        if ($context === null) {
            return null;
        } elseif (
            $contextSource && isset($settingSchema[$contextSource]) && $settingSchema[$contextSource]['type'] === 'array')
        {
            $generatedValues = [];
            foreach ($context as $k => $itemContext) {
                if ($this->expressionLanguage->evaluate($valueExpression->getConditionExpression(), $itemContext) === true) {
                    $generatedValue = $this->expressionLanguage->evaluate($valueExpression->getFormatExpression(), $itemContext);
                    if ($generatedValue) {
                        $generatedValues[$k] = is_string($generatedValue) ? trim($generatedValue) : $generatedValue;
                    }
                }
            }

            return $generatedValues;
        } else {
            if ($this->expressionLanguage->evaluate($valueExpression->getConditionExpression(), $context) === true) {
                $generatedValue = $this->expressionLanguage->evaluate($valueExpression->getFormatExpression(), $context);

                return is_string($generatedValue) ? trim($generatedValue) : $generatedValue;
            }
        }

        return null;
    }
}