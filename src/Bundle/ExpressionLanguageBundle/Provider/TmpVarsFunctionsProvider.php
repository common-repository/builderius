<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Bundle\ExpressionLanguageBundle\SafeCallable;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class TmpVarsFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    protected $context;

    /**
     * @param BuilderiusRuntimeObjectCache $context
     * @return $this
     */
    public function setTmpVarsContext(BuilderiusRuntimeObjectCache $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'createTempVariable',
                function ($context, $name, $value) {
                    return sprintf('createTempVariable(%s, %s)', $name, $value);
                },
                function ($context, $name, $value) {
                    try {
                        if (is_array($value) && (!isset($value[0]) || empty($value))) {
                            $value = (object)$value;
                        }
                        $this->context->set($name, $value);

                        return true;
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            ),
            new ExpressionFunction(
                'tempVariable',
                function ($context, $name) {
                    return sprintf('tempVariable(%s)', $name);
                },
                function ($context, $name) {
                    return $this->context->get($name);
                }
            )
        ];
    }
}