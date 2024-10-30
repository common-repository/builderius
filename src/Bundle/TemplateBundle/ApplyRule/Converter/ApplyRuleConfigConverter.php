<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Converter;

use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Symfony\Component\PropertyAccess\PropertyAccess;

class ApplyRuleConfigConverter
{
    /**
     * @param array $configSet
     * @param array $convertedConfig
     * @param string $expression
     * @param string $conjunctionsPath
     * @param int $index
     * @return array
     * @throws \Exception
     */
    public static function convert(array $configSet, $convertedConfig = [], $expression = '', $conjunctionsPath = '', $index = 0)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if (isset($configSet['condition']) && in_array($configSet['condition'], BuilderiusTemplateApplyRulesChecker::CONJUNCTIONS)) {
            if (!array_key_exists('rules', $configSet)) {
                throw new \Exception('Apply Rule Config is not correct.');
            }
            if (empty($conjunctionsPath)) {
                $conjunctionsPath = sprintf('%s[%s]', $conjunctionsPath, $configSet['condition']);
            } else {
                $conjunctionsPath = sprintf('%s[%d][%s]', $conjunctionsPath, $index, $configSet['condition']);
            }
            $propertyAccessor->setValue($convertedConfig, $conjunctionsPath, []);
            if (isset($configSet['name']) && array_key_exists('value', $configSet)) {
                $expression = empty($expression) ? $configSet['name'] : sprintf('%s.%s', $expression, $configSet['name']);
            }
            foreach ($configSet['rules'] as $k => $someConfig) {
                $convertedConfig = self::convert($someConfig, $convertedConfig, $expression, $conjunctionsPath, $k);
            }
        } elseif (isset($configSet['operator']) && in_array($configSet['operator'], BuilderiusTemplateApplyRulesChecker::OPERATORS)) {
            if (isset($configSet['name']) && array_key_exists('value', $configSet)) {
                $var = empty($expression) ? $configSet['name'] : sprintf('%s.%s', $expression, $configSet['name']);
                $cnt = count($propertyAccessor->getValue($convertedConfig, $conjunctionsPath));
                $propertyAccessor->setValue(
                    $convertedConfig,
                    sprintf('%s[%s][%s]', $conjunctionsPath, $cnt, $configSet['operator']),
                    [
                        0 => ['var' => $var],
                        1 => $configSet['value']
                    ]
                );
            }
        }

        return $convertedConfig;
    }
}