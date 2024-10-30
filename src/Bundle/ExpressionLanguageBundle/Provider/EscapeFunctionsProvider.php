<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class EscapeFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'escape',
                function ($context, $data = '') {
                    return sprintf('escape(%s)', $data);
                },
                function ($context, $data = '') {
                    return esc_attr($data);
                }
            ),
        ];
    }
}