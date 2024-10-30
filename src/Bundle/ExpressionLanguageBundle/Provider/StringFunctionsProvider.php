<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class StringFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'limit_characters',
                function ($context, $string, $length, $offset = 0) {
                    return sprintf('limit_characters(%s, %s, %s)', $string, $length, $offset);
                },
                function ($context, $string, $length, $offset = 0) {
                    try {
                        if ($offset >= mb_strlen($string)) {
                            return '';
                        }

                        return mb_substr($string, $offset, $length);
                    } catch (\Exception $e) {
                        return $string;
                    }
                }
            ),
            new ExpressionFunction(
                'limit_words',
                function ($context, $string, $length, $offset = 0) {
                    return sprintf('limit_words(%s, %s, %s)', $string, $length, $offset);
                },
                function ($context, $string, $length, $offset = 0) {
                    try {
                        $words = explode(' ', $string);
                        if ($offset >= count($words)) {
                            return '';
                        }
                        $limitedWords = array_slice($words, $offset, $length);

                        return implode(' ', $limitedWords);
                    } catch (\Exception $e) {
                        return $string;
                    }
                }
            )
        ];
    }
}