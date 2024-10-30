<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Builderius\Symfony\Component\ExpressionLanguage\Lexer;
use Builderius\Symfony\Component\ExpressionLanguage\Token;

class ExpressionDataVarValueGenerator implements DataVarValueGeneratorInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param ExpressionLanguage $expressionLanguage
     * @param Lexer $lexer
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(ExpressionLanguage $expressionLanguage, Lexer $lexer, BuilderiusRuntimeObjectCache $cache)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->lexer = $lexer;
        $this->cache = $cache;
    }


    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'expression';
    }

    /**
     * @inheritDoc
     */
    public function getDependsOnDataVars(array $dataVarConfig)
    {
        if ($dataVarConfig['name'] === 'no_cache') {
            $dependsOnDataVars = false;
        } else {
            $dependsOnDataVars = $this->cache->get(sprintf('%s_depends_on_data_vars', $dataVarConfig['name']));
        }
        if (false === $dependsOnDataVars) {
            $dependsOnDataVars = [];
            try {
                $expression = $dataVarConfig['value'];
                $tokensStream = $this->lexer->tokenize($expression);
                $prev = null;
                do {
                    if (isset($current)) {
                        $prev = $current;
                    }
                    /** @var Token $current */
                    $current = $tokensStream->current;
                    if ($current->type === 'string') {
                        $subTokensStream = $this->lexer->tokenize(str_replace(['\'', '\\', '"'], '', $current->value));
                        $internalExpression = false;
                        do {
                            if ($subTokensStream->current->type === 'punctuation' || $subTokensStream->current->type === 'operator') {
                                $internalExpression = true;
                                break;
                            } else {
                                $subTokensStream->next();
                            }
                        } while (!$subTokensStream->isEOF());
                        if ($internalExpression === true) {
                            $subDependsOnDataVars = $this->getDependsOnDataVars([
                                'name' => 'no_cache',
                                'type' => 'expression',
                                'value' => $current->value
                            ]);
                            foreach ($subDependsOnDataVars as $subDep) {
                                if (!in_array($subDep, $dependsOnDataVars)) {
                                    $dependsOnDataVars[] = $subDep;
                                }
                            }
                        }
                    }
                    $tokensStream->next();
                    $next = $tokensStream->current;
                    if ($current->type === 'name' && $next->value !== '(' && (!$prev || ($prev->type !== 'punctuation' || in_array($prev->value, ['[', '(', ',', ':', ' '])))) {
                        if (!in_array($current->value, $dependsOnDataVars)) {
                            $dependsOnDataVars[] = $current->value;
                        }
                    }
                } while (!$tokensStream->isEOF());
                if ($dataVarConfig['name'] !== 'no_cache') {
                    $this->cache->set(sprintf('%s_depends_on_data_vars', $dataVarConfig['name']), $dependsOnDataVars);
                }
            } catch (\Exception $e) {
                return $dependsOnDataVars;
            }
        }

        return $dependsOnDataVars;
    }

    /**
     * @inheritDoc
     */
    public function generateValue($templateType, $dataVarName, array $dataVarsConfigs, array $dataVarsValues)
    {
        $dataVarConfig = $dataVarsConfigs[$dataVarName];
        $context = $dataVarsValues;
        foreach ($this->getDependsOnDataVars($dataVarConfig) as $dependsOnDataVarName) {
            if (isset($dataVarsValues[$dependsOnDataVarName])) {
                $context[$dependsOnDataVarName] = $dataVarsValues[$dependsOnDataVarName];
            } else {
                $dataVarsValues[$dependsOnDataVarName] = null;
            }
        }
        try {
            $value = $this->expressionLanguage->evaluate($dataVarConfig['value'], $context);
            $dataVarsValues[$dataVarName] = $value;
        } catch (\Exception|\Error $e) {
            $dataVarsValues[$dataVarName] = null;
        }

        return $dataVarsValues;
    }
}