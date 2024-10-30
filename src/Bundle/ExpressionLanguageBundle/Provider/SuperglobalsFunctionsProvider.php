<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Bundle\ExpressionLanguageBundle\SafeCallable;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SuperglobalsFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'superglobalVariable',
                function ($context, $type, $key, $fallback = null) {
                    return sprintf('superglobalVariable(%s, %s, %s)', $type, $key, $fallback);
                },
                function ($context, $type, $key, $fallback = null) {
                    switch ($type) {
                        case 'GET':
                            return array_key_exists($key, $_GET) ? (is_string($_GET[$key]) ? sanitize_text_field($_GET[$key]) : $_GET[$key]) : $fallback;
                        case 'POST':
                            return array_key_exists($key, $_POST) ? (is_string($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $_POST[$key]) : $fallback;
                        case 'COOKIE':
                            return array_key_exists($key, $_COOKIE) ? (is_string($_COOKIE[$key]) ? sanitize_text_field($_COOKIE[$key]) : $_COOKIE[$key]) : $fallback;
                        default:
                            return $fallback;
                    }
                }
            )
        ];
    }
}