<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Bundle\ExpressionLanguageBundle\SafeCallable;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Builderius\Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'includes',
                function ($context, $el, $arr) {
                    return sprintf('includes(%s, %s)', $el, $arr);
                },
                function ($context, $el, $arr) {
                    try {
                        if (is_array($arr) || is_object($arr)) {
                            return in_array($el, (array)$arr);
                        } else {
                            return str_contains($arr, $el);
                        }
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            ),
            new ExpressionFunction(
                'sum',
                function ($context, $arr) {
                    return sprintf('sum(%s)', $arr);
                },
                function ($context, $arr) {
                    try {
                        if (is_array($arr) || is_object($arr)) {
                            return array_sum((array)$arr);
                        } else {
                            return strlen((string)$arr);
                        }
                    } catch (\Exception $e) {
                        return 0;
                    }
                }
            ),
            new ExpressionFunction(
                'count',
                function ($context, $arr) {
                    return sprintf('count(%s)', $arr);
                },
                function ($context, $arr) {
                    try {
                        if (is_array($arr) || is_object($arr)) {
                            return count((array)$arr);
                        } else {
                            return strlen((string)$arr);
                        }
                    } catch (\Exception $e) {
                        return 0;
                    }
                }
            ),
            new ExpressionFunction(
                'findIndex',
                function () {
                    return sprintf(
                        'findIndex(%s)',
                        implode(', ', func_get_args())
                    );
                },
                function ($context, $array, SafeCallable $callback) {
                    foreach ($array as $key => $value) {
                        $result = $callback->call($value, $key);
                        if (true === $result) {
                            return $key;
                        }
                    }

                    return -1;
                }
            ),
            new ExpressionFunction(
                'split',
                function ($context, $val, $char) {
                    return sprintf('split(%s, %s)', $val, $char);
                },
                function ($context, $val, $char) {
                    try {
                        return explode($char, $val);
                    } catch (\Exception $e) {
                        return [];
                    }
                }
            ),
            new ExpressionFunction(
                'joinKeys',
                function ($context, $variables, $joinOperator) {
                    return sprintf('join(%s, %s)', $variables, $joinOperator);
                },
                function ($context, $variables, $joinOperator) {
                    if (!$joinOperator) {
                        throw new \Exception('missing joinOperator');
                    }
                    if (is_array($variables) && !empty($variables)) {
                        return implode($joinOperator, array_keys($variables));
                    } else {
                        return null;
                    }
                }
            ),
            new ExpressionFunction(
                'joinValues',
                function ($context, $variables, $joinOperator) {
                    return sprintf('joinValues(%s, %s)', $variables, $joinOperator);
                },
                function ($context, $variables, $joinOperator) {
                    if (!$joinOperator) {
                        throw new \Exception('missing joinOperator');
                    }
                    if (is_array($variables) && !empty($variables)) {
                        return implode($joinOperator, $variables);
                    } else {
                        return null;
                    }
                }
            ),
            new ExpressionFunction(
                'merge',
                function ($context, ...$arrays) {
                    return sprintf('merge(%s, %s)', $arrays);
                },
                function ($context, ...$arrays) {
                    $final = [];
                    foreach ($arrays as $k => $value) {
                        if ($k === 0) {
                            $final = $value;
                        } else {
                            foreach ($value as $i => $v) {
                                if (is_array($final)) {
                                    $final[$i] = $v;
                                } elseif($final instanceof \stdClass) {
                                    $final->$i = $v;
                                }
                            }
                        }
                    }
                    return $final;
                }
            ),
            new ExpressionFunction(
                'foreach',
                function () {
                    return sprintf(
                        'foreach(%s)',
                        implode(', ', func_get_args())
                    );
                },
                function ($context, $array, SafeCallable $callback, $keepKeys = true) {
                    $newArray = [];
                    foreach ($array as $key => $value) {
                        if (true === $keepKeys) {
                            $newArray[$key] = $callback->call($value, $key);
                        } else {
                            $newArray[] = $callback->call($value, $key);
                        }
                    }

                    return $newArray;
                }
            ),
            new ExpressionFunction(
                'filter',
                function () {
                    return sprintf(
                        'filter(%s)',
                        implode(', ', func_get_args())
                    );
                },
                function ($context, array $array, SafeCallable $callback) {
                    $newArray = [];
                    foreach ($array as $key => $value) {
                        $result = $callback->call($value, $key);
                        if (true === $result) {
                            $newArray[] = $value;
                        }
                    }

                    return $newArray;
                }
            ),
            new ExpressionFunction(
                'push',
                function ($context, $array, $element) {
                    return sprintf('push(%s, %s)', $array, $element);
                },
                function ($context, $array, $element) {
                    if (is_array($element)) {
                        if (array_keys($element) !== range(0, count($element) - 1)) {
                            $element = (object)$element;
                        }
                    }
                    array_push($array, $element);

                    return $array;
                }
            ),
            new ExpressionFunction(
                'unshift',
                function ($context, $array, $element) {
                    return sprintf('unshift(%s, %s)', $array, $element);
                },
                function ($context, $array, $element) {
                    if (is_array($element)) {
                        if (array_keys($element) !== range(0, count($element) - 1)) {
                            $element = (object)$element;
                        }
                    }
                    array_unshift($array, $element);

                    return $array;
                }
            ),
            new ExpressionFunction(
                'get',
                function ($context, $array, $offset, $fallback = null) {
                    return sprintf('get(%s, %s, %s)', $array, $offset, $fallback);
                },
                function ($context, $array, $offset, $fallback = null) {
                    try {
                        $propertyAccessor = PropertyAccess::createPropertyAccessor();
                        $value = $propertyAccessor->getValue($array, $offset);

                        return $value ?: $fallback;
                    } catch (\Exception $e) {
                        return $fallback;
                    }
                }
            ),
            new ExpressionFunction(
                'set',
                function ($context, $array, $offset, $value) {
                    return sprintf('set(%s, %s, %s)', $array, $offset, $value);
                },
                function ($context, $array, $offset, $value) {
                    if (is_array($value)) {
                        if (array_keys($value) !== range(0, count($value) - 1)) {
                            $value = (object)$value;
                        }
                    }
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    $propertyAccessor->setValue($array, $offset, $value);

                    return $array;
                }
            ),
            new ExpressionFunction(
                'unset',
                function ($context, $array, $offset) {
                    return sprintf('unset(%s, %s)', $array, $offset);
                },
                function ($context, $array, $offset) {
                    $path = explode('.', str_replace('..', '.', str_replace('[', '.', str_replace(']', '', $offset))));
                    $temp = & $array;

                    foreach($path as $key) {
                        if (is_object($temp)) {
                            if (property_exists($temp, $key)) {
                                if (!($key == end($path))) $temp = &$temp->$key;
                            } else {
                                return false;
                            }
                        } else {
                            if (isset($temp[$key])) {
                                if (!($key == end($path))) $temp = &$temp[$key];
                            } else {
                                return false;
                            }
                        }
                    }
                    if (is_array($temp)) {
                        unset($temp[end($path)]);
                    } else {
                        unset($temp->{end($path)});
                    }

                    return $array;
                }
            ),
            new ExpressionFunction(
                'serialize',
                function ($context, $array) {
                    return sprintf('serialize(%s)', $array);
                },
                function ($context, $array) {
                    if (is_array($array) || is_object($array)) {
                        return json_encode($array, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                    }

                    return $array;
                }
            ),
            new ExpressionFunction(
                'unserialize',
                function ($context, $string) {
                    return sprintf('unserialize(%s)', $string);
                },
                function ($context, $string) {
                    $array = json_decode($string);
                    if ($array === null && json_last_error() !== JSON_ERROR_NONE) {
                        return [];
                    }

                    return $array;
                }
            ),
            new ExpressionFunction(
                'isset',
                function ($context, $array, $key) {
                    return sprintf('isset(%s, %s)', $array, $key);
                },
                function ($context, $array, $key) {
                    try {
                        $array = (array)$array;

                        return array_key_exists($key, $array);
                    } catch (\Exception $e) {
                        return false;
                    }

                }
            ),
            new ExpressionFunction(
                'addElement',
                function ($context, $array, $key, $value) {
                    return sprintf('addElement(%s, %s, %s)', $array, $key, $value);
                },
                function ($context, $array, $key, $value) {
                    $array[$key] = $value;

                    return $array;
                }
            ),
            new ExpressionFunction(
                'group_by',
                function ($context, $array, $key) {
                    return sprintf('group_by(%s, %s)', $array, $key);
                },
                function ($context, $array, $key) {
                    $result = [];

                    foreach($array as $val) {
                        if(array_key_exists($key, $val)){
                            $result[$val[$key]][] = $val;
                        }else{
                            $result[""][] = $val;
                        }
                    }

                    return $result;
                }
            ),
            new ExpressionFunction(
                'sort',
                function ($context, $array) {
                    return sprintf('sort(%s)', $array);
                },
                function ($context, $array) {
                    if(!is_array($array)) {
                        return $array;
                    }
                    sort($array);

                    return $array;
                }
            ),
            new ExpressionFunction(
                'rsort',
                function ($context, $array) {
                    return sprintf('rsort(%s)', $array);
                },
                function ($context, $array) {
                    if(!is_array($array)) {
                        return $array;
                    }
                    rsort($array);

                    return $array;
                }
            ),
            new ExpressionFunction(
                'usort',
                function () {
                    return sprintf(
                        'usort(%s)',
                        implode(', ', func_get_args())
                    );
                },
                function ($context, $array, SafeCallable $callback) {
                    if(!is_array($array)) {
                        return $array;
                    }
                    usort($array, function($a, $b) use ($callback) {
                        return $callback->call($a, $b);
                    });

                    return $array;
                }
            )
        ];
    }
}