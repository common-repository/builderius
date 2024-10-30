<?php

namespace Builderius\Bundle\ModuleBundle\ExpressionLanguage\Provider;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Event\DynamicDataConditionEvaluationEvent;
use Builderius\Bundle\ModuleBundle\RenderingCondition\BuilderiusModuleRenderingConditionsProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Builderius\Symfony\Component\Templating\EngineInterface;

class VisibilityConditionsFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var BuilderiusModuleRenderingConditionsProviderInterface
     */
    private $moduleRenderingConditionsProvider;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param ExpressionLanguage $expressionLanguage
     * @param BuilderiusModuleRenderingConditionsProviderInterface $moduleRenderingConditionsProvider
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        ExpressionLanguage $expressionLanguage,
        BuilderiusModuleRenderingConditionsProviderInterface $moduleRenderingConditionsProvider,
        EventDispatcher $eventDispatcher
    ) {
        $this->expressionLanguage = $expressionLanguage;
        $this->moduleRenderingConditionsProvider = $moduleRenderingConditionsProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'processVisibilityCondition',
                function ($context, $condConfig) {
                    return sprintf('processVisibilityCondition(%s)', $condConfig);
                },
                function ($context, $condConfig) {
                    try {
                        if (in_array($condConfig, [null, ''])) {
                            return 'true == true';
                        } elseif (is_string($condConfig)) {
                            return $condConfig;
                        } else {
                            if ($condConfig['type'] === 'group') {
                                $expr = $this->processGroup($condConfig['rules'], $condConfig['condition']);

                                return $expr;
                            }
                        }
                    } catch (\Exception $e) {
                        return 'true == true';
                    }

                    return 'true == true';
                }
            ),
            new ExpressionFunction(
                'compare_dynamic_data',
                function ($context, $value1, $value2, $operator) {
                    return sprintf('compare_dynamic_data(%s, %s, %s)', $value1, $value2, $operator);
                },
                function ($context, $value1, $value2, $operator) {
                    try {
                        $data = [];
                        $data['a'] = $this->processValue($value1);
                        $data['b'] = $this->processValue($value2);
                        $finalExpression = 'a ' . $operator . ' b';
                        if ('contains' === $operator) {
                            $finalExpression = 'includes(b, a)';
                        } else if ('does_not_contain' === $operator) {
                            $finalExpression = '!includes(b, a)';
                        } else if ('is_empty' === $operator) {
                            $finalExpression = 'is_empty(a)';
                        } else if ('is_not_empty' === $operator) {
                            $finalExpression = '!is_empty(a)';
                        } else if ('is_null' === $operator) {
                            $finalExpression = 'is_null(a)';
                        } else if ('is_not_null' === $operator) {
                            $finalExpression = '!is_null(a)';
                        }
                        if ('is_true' === $operator) {
                            return 'true' === $data['a'] || true === $data['a'];
                        } else if ('is_not_true' === $operator) {
                            return 'true' !== $data['a'] || true !== $data['a'];
                        }else if ('is_false' === $operator) {
                            return 'false' === $data['a'] || false === $data['a'];
                        }else if ('is_not_false' === $operator) {
                            return 'false' !== $data['a'] || false !== $data['a'];
                        } else if ('==' === $operator) {
                            return $data['a'] === $data['b'];
                        } else if ('!=' === $operator) {
                            return $data['a'] !== $data['b'];
                        } else if ('<' === $operator) {
                            return $data['a'] < $data['b'];
                        } else if ('<=' === $operator) {
                            return $data['a'] <= $data['b'];
                        } else if ('>' === $operator) {
                            return $data['a'] > $data['b'];
                        } else if ('>=' === $operator) {
                            return $data['a'] >= $data['b'];
                        } else {
                            return $this->expressionLanguage->evaluate($finalExpression, $data);
                        }
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            )
        ];
    }

    /**
     * @param string $value
     * @return string
     */
    private function processValue($value)
    {
        if ($value === "''" || $value === "\"\"") {
            return "";
        }
        if (
            strpos($value, 'builderius_data_var') !== false ||
            strpos($value, 'builderius_data_var_escaped') !== false ||
            strpos($value, 'builderius_tp_data_var') !== false ||
            strpos($value, 'builderius_tp_data_var_escaped') !== false
        ) {
            $value = str_replace('[^', '', str_replace('|raw^]', '', $value));
            preg_match_all('/builderius_data_var\(\'(.*?)\'\)/s', $value, $nonEscapedDataVars);
            $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[1]);
            foreach ($nonEscapedDataVarsNames as $nonEscapedDataVarName) {
                $event = new DynamicDataConditionEvaluationEvent('builderius_data_var', [$nonEscapedDataVarName]);
                $this->eventDispatcher->dispatch($event, 'builderius_dynamic_data_condition_evaluation');
                $dataVarValue = $event->getValue();
                if ($value === sprintf("builderius_data_var('%s')", $nonEscapedDataVarName)) {
                    $value = $dataVarValue;
                } else {
                    $value = str_replace(
                        sprintf("builderius_data_var('%s')", $nonEscapedDataVarName),
                        $dataVarValue,
                        $value
                    );
                }
            }

            preg_match_all('/builderius_data_var_escaped\(\'(.*?)\'\)/s', $value, $escapedDataVars);
            $escapedDataVarsNames = array_unique($escapedDataVars[1]);
            foreach ($escapedDataVarsNames as $escapedDataVarName) {
                $event = new DynamicDataConditionEvaluationEvent('builderius_data_var_escaped', [$escapedDataVarName]);
                $this->eventDispatcher->dispatch($event, 'builderius_dynamic_data_condition_evaluation');
                $dataVarValue = $event->getValue();
                if ($value === sprintf("builderius_data_var_escaped('%s')", $escapedDataVarName)) {
                    $value = $dataVarValue;
                } else {
                    $value = str_replace(
                        sprintf("builderius_data_var_escaped('%s')", $escapedDataVarName),
                        $dataVarValue,
                        $value
                    );
                }
            }

            preg_match_all('/builderius_tp_data_var\(\'(.*?)\'\)/s', $value, $nonEscapedTpDataVars);
            $nonEscapedTpDataVarsNames = array_unique($nonEscapedTpDataVars[1]);
            foreach ($nonEscapedTpDataVarsNames as $nonEscapedTpDataVarName) {
                $args = explode("', '", ltrim(rtrim($nonEscapedTpDataVarName, "'"), "'"));
                $event = new DynamicDataConditionEvaluationEvent('builderius_tp_data_var', $args);
                $this->eventDispatcher->dispatch($event, 'builderius_dynamic_data_condition_evaluation');
                $dataVarValue = $event->getValue();
                if ($value === sprintf("builderius_tp_data_var('%s','%s')", $args[0], $args[1])) {
                    $value = $dataVarValue;
                } else {
                    $value = str_replace(
                        sprintf("builderius_tp_data_var('%s','%s')", $args[0], $args[1]),
                        $dataVarValue,
                        $value
                    );
                }
            }

            preg_match_all('/builderius_tp_data_var_escaped\(\'(.*?)\'\)/s', $value, $escapedTpDataVars);
            $escapedTpDataVarsNames = array_unique($escapedTpDataVars[1]);
            foreach ($escapedTpDataVarsNames as $escapedTpDataVarName) {
                $args = explode("', '", ltrim(rtrim($escapedTpDataVarName, "'"), "'"));
                $event = new DynamicDataConditionEvaluationEvent('builderius_tp_data_var_escaped', $args);
                $this->eventDispatcher->dispatch($event, 'builderius_dynamic_data_condition_evaluation');
                $dataVarValue = $event->getValue();
                if ($value === sprintf("builderius_tp_data_var_escaped('%s','%s')", $args[0], $args[1])) {
                    $value = $dataVarValue;
                } else {
                    $value = str_replace(
                        sprintf("builderius_tp_data_var_escaped('%s','%s')", $args[0], $args[1]),
                        $dataVarValue,
                        $value
                    );
                }
            }
        }

        if (is_string($value)) {
            return str_replace('"', '\\\\"', (string)$value);
        } else {
            return $value;
        }
    }

    /**
     * @param array $subConfigs
     * @param string $conjunction
     * @return bool
     */
    private function processGroup(array $rulesConfigs, $conjunction) {
        $subRulesResults = [];
        foreach ($rulesConfigs as $ruleConfig) {
            if ($ruleConfig['type'] === 'group') {
                $subRulesResults[] = $this->processGroup($ruleConfig['rules'], $ruleConfig['condition']);
            } else {
                $subRulesResults[] = '(' . $this->processSingle($ruleConfig) . ')';
            }
        }
        if (count($subRulesResults) > 1) {
            return '(' . implode(' '. $conjunction . ' ', $subRulesResults) . ')';
        } else {
            return reset($subRulesResults);
        }
    }

    /**
     * @param array $ruleConfig
     * @return bool
     */
    private function processSingle(array $ruleConfig)
    {
        $name = $ruleConfig['name'];
        if (strpos($name, '.') !== false) {
            $name = explode('.', $ruleConfig['name'])[1];
        }
        $renderCond = $this->moduleRenderingConditionsProvider->getRenderingCondition($name);
        if ($renderCond) {
            $expression = $renderCond->getExpression();
            try {
                if (is_bool($ruleConfig['value'])) {
                    $ruleConfig['value'] = $ruleConfig['value'] === true ? 'true' : 'false';
                }
                if ($ruleConfig['name'] === 'dynamic_data') {
                    if (!isset($ruleConfig['value']['a1'])) {
                        $ruleConfig['value']['a1'] = null;
                    }
                    if (!isset($ruleConfig['value']['b1'])) {
                        $ruleConfig['value']['b1'] = null;
                    }
                }
                $expr = $this->expressionLanguage->evaluate($expression, $ruleConfig);
                if (strpos($expr, 'compare_dynamic_data') !== false) {
                    $argsStr = rtrim(ltrim($expr, 'compare_dynamic_data("'), '")');
                    $args = explode('", "', $argsStr);
                    if ($args[1] === "''" || $args[1] === "\"\"") {
                        $args[1] = "";
                    }
                    $expr = sprintf('compare_dynamic_data("%s", "%s", "%s")', str_replace('"', '\\\\"', $args[0]), str_replace('"', '\\\\"', $args[1]), $args[2]);
                }

                return $expr;
            } catch (\Exception $e) {
                return 'true === true';
            }
        }

        return 'true === true';
    }
}