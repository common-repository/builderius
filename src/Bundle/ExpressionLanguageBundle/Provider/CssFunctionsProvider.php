<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CssFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'join',
                function ($context, $variables, $joinOperator = ' ', $joinOperatorAtEnd = false) {
                    return sprintf('join(%s, %s)', $variables, $joinOperator);
                },
                function ($context, $variables, $joinOperator = ' ', $joinOperatorAtEnd = false) {
                    if (!$joinOperator) {
                        throw new \Exception('missing joinOperator');
                    }
                    if (is_array($variables) && !empty($variables)) {
                        return $joinOperatorAtEnd === false ?
                            implode($joinOperator, $variables) :
                            sprintf('%s%s', implode($joinOperator, $variables), $joinOperator);
                    } else {
                        return null;
                    }
                }
            ),
            new ExpressionFunction(
                'css_var',
                function ($context, $variables, $fallback) {
                    return sprintf('css_var(%s, %s)', $variables, $fallback);
                },
                function ($context, $variables, $fallback) {
                    if (is_array($variables) && !empty($variables)) {
                        $reversedVariables = array_reverse($variables);
                        $result = '';
                        foreach ($reversedVariables as $i => $variable) {
                            if ($i === 0) {
                                if ($fallback !== null && $fallback !== '') {
                                    $result = sprintf('var(--%s,%s)', str_replace('--', '', $variable), trim($fallback));
                                } else {
                                    $result = sprintf('var(--%s)', str_replace('--', '', $variable));
                                }
                            } else {
                                $result = sprintf('var(--%s,%s)', str_replace('--', '', $variable), $result);
                            }
                        }

                        return $result;
                    } elseif ($fallback !== null && trim($fallback) !== '') {
                        return trim($fallback);
                    }

                    return null;
                }
            ),
            new ExpressionFunction(
                'bg_img_gradient',
                function ($context, $array) {
                    return sprintf('bg_img_gradient(%s)', $array);
                },
                function ($context, $array) {
                    if (is_array($array) && !empty($array)) {
                        usort($array, function ($a, $b) {
                            return ($a['pos'] < $b['pos']) ? -1 : 1;
                        });
                        $gradient = [];
                        foreach ($array as $variable) {
                            $gradient[] = sprintf('%s %d%%', $variable['code'], $variable['pos']);
                        }

                        return implode(', ', $gradient);
                    }

                    return null;
                }
            ),
        ];
    }
}