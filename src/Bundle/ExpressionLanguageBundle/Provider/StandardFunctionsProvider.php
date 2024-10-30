<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class StandardFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @param string $phpFunctionName
     * @param string|null $expressionFunctionName
     * @param mixed|null $returnOnError
     * @return ExpressionFunction
     */
    private static function fromPhp (string $phpFunctionName, string $expressionFunctionName = null, $returnOnError = null)
    {
        if (null === $returnOnError) {
            return ExpressionFunction::fromPhp($phpFunctionName, $expressionFunctionName);
        } else {
            try {
                return ExpressionFunction::fromPhp($phpFunctionName, $expressionFunctionName);
            } catch (\Exception $e) {
                return $returnOnError;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            self::fromPhp('ceil', null, 0),
            self::fromPhp('floor', null, 0),
            self::fromPhp('strtolower', 'lower', ''),
            self::fromPhp('strtoupper', 'upper', ''),
            self::fromPhp('sprintf', null, ''),
            self::fromPhp('round', null, 0),
            self::fromPhp('max', null, 0),
            self::fromPhp('min', null, 0),
            self::fromPhp('date', null, 0),
            self::fromPhp('strtotime', null, 0),
            self::fromPhp('number_format', null, 0),
            self::fromPhp('is_null', null, 0),
            self::fromPhp('is_float', null, 0),
            self::fromPhp('is_int', null, 0),
            self::fromPhp('floatval', null, 0),
            self::fromPhp('intval', null, 0),
            self::fromPhp('array_column', 'pluck', 0),
            new ExpressionFunction(
                'is_email',
                function ($context, $email) {
                    return sprintf('is_email(%s)', $email);
                },
                function ($context, $email) {
                    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
                }
            ),
            new ExpressionFunction(
                'isEmail',
                function ($context, $email) {
                    return sprintf('isEmail(%s)', $email);
                },
                function ($context, $email) {
                    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
                }
            ),
            new ExpressionFunction(
                'is_url',
                function ($context, $url) {
                    return sprintf('is_url(%s)', $url);
                },
                function ($context, $url) {
                    return (bool)filter_var($url, FILTER_VALIDATE_URL);
                }
            ),
            new ExpressionFunction(
                'is_empty',
                function ($context, $var) {
                    return sprintf('is_empty(%s)', $var);
                },
                function ($context, $var) {
                    try {
                        return empty($var);
                    } catch (\Exception $e) {
                        return true;
                    }

                }
            ),
            new ExpressionFunction(
                'sanitize_graphql_alias',
                function ($context, $var) {
                    return sprintf('sanitize_graphql_alias(%s)', $var);
                },
                function ($context, $var) {
                    return strtolower(preg_replace('/[^\\w]+/', '_', $var, -1));
                }
            ),
            new ExpressionFunction(
                'match',
                function ($context, $regex, $string) {
                    return sprintf('match(%s, %s)', $regex, $string);
                },
                function ($context, $regex, $string) {
                    $possibleModifiers = ['m', 'i', 'A', 'u', 's'];
                    try {
                        $modifiers = substr(strrchr($regex, '/'), 1);
                        if (empty($modifiers)) {
                            preg_match($regex, $string, $matches);
                            if (!empty($matches)) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            $global = false;
                            $modifiersArr = str_split($modifiers);
                            $filteredModifiers = [];
                            foreach ($modifiersArr as $modifier) {
                                if ('g' === $modifier) {
                                    $global = true;
                                } elseif('y' === $modifier) {
                                    $filteredModifiers[] = 'A';
                                } elseif (in_array($modifier, $possibleModifiers)) {
                                    $filteredModifiers[] = $modifier;
                                }
                            }
                            $filteredModifiers = implode('', $filteredModifiers);
                            $regex = str_replace('/' . $modifiers, '/' . $filteredModifiers, $regex);
                            if (!$global) {
                                preg_match($regex, $string, $matches);
                                if (!empty($matches)) {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                preg_match_all($regex, $string, $matches);
                                if (!empty($matches)) {
                                    foreach ($matches as $match) {
                                        if (!empty($match)) {
                                            return true;
                                        }
                                    }

                                }
                                return false;
                            }
                        }
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            ),
        ];
    }
}