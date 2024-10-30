<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class MultilingualFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        EventDispatcher $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'translate',
                function ($context, $string, $domain = 'builderius_strings') {
                    return sprintf('translate(%s, %s)', $string, $domain);
                },
                function ($context, $string, $domain = 'builderius_strings') {
                    try {
                        $this->eventDispatcher->dispatch(new ConfigContainingEvent([]), 'builderius_before_string_translation');
                        return __($string, $domain);
                    } catch (\Exception $e) {
                        return $string;
                    }
                }
            )
        ];
    }
}