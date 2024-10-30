<?php

namespace Builderius\Bundle\TemplateBundle\Twig;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\DataVar\DataVarsFinalValuesProviderInterface;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Builderius\Symfony\Component\ExpressionLanguage\Lexer;
use Builderius\Symfony\Component\ExpressionLanguage\Token;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFilter;
use Builderius\Twig\TwigFunction;

class TemplateDataVarsExtension extends AbstractExtension
{
    const NAME = 'builderius_template_data_vars';
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
     * @var DataVarsFinalValuesProviderInterface
     */
    private $dataVarsFinalValuesProvider;

    /**
     * @param ExpressionLanguage $expressionLanguage
     * @param Lexer $lexer
     * @param BuilderiusRuntimeObjectCache $cache
     * @param DataVarsFinalValuesProviderInterface $dataVarsFinalValuesProvider
     */
    public function __construct(
        ExpressionLanguage $expressionLanguage,
        Lexer $lexer,
        BuilderiusRuntimeObjectCache $cache,
        DataVarsFinalValuesProviderInterface $dataVarsFinalValuesProvider
    ) {
        $this->expressionLanguage = $expressionLanguage;
        $this->lexer = $lexer;
        $this->cache = $cache;
        $this->dataVarsFinalValuesProvider = $dataVarsFinalValuesProvider;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'builderius_data_var',
                [$this, 'getNonEscapedDataVarValue']
            ),
            new TwigFunction(
                'builderius_data_var_escaped',
                [$this, 'getEscapedDataVarValue']
            ),
            new TwigFunction(
                'builderius_dynamic_css_var',
                [$this, 'getDynamicCssVarValue']
            ),
            new TwigFunction(
                'builderius_visibility_condition',
                [$this, 'evaluateVisibilityCondition']
            ),
            new TwigFunction(
                'builderius_visibility_condition_for_css_identifier',
                [$this, 'getVisibilityConditionForCssIdentifier']
            )
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter(
                'esc_attr',
                [$this, 'escapeAttributes']
            ),
            new TwigFilter(
                'escape_quotes',
                [$this, 'escapeQuotes']
            )
        ];
    }

    /**
     * @param $data
     * @return string|void
     */
    public function escapeAttributes($data)
    {
        $attr = $data;
        if (is_object($data)) {
            $data = (array)$data;
        }
        if (is_array($data)) {
            $attr = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        }
        if (is_bool($attr)) {
            $attr = ($attr === true) ? 'true' : 'false';
        }

        return esc_attr($attr);
    }

    /**
     * @param string $dataVarExpression
     * @return mixed
     * @throws \Exception
     */
    public function getDataVarValue($dataVarExpression)
    {

        $dataVarExpression = str_replace(
            "&quot;",
            "\"",
            str_replace(
                "&#039;",
                "'",
                $dataVarExpression
            )
        );
        $dataVarExpression = htmlspecialchars_decode($dataVarExpression, ENT_NOQUOTES);
        try {
            $dataVarConfig = [
                'name' => 'no_cache',
                'type' => 'expression',
                'value' => $dataVarExpression
            ];
            $context = [];
            foreach ($this->getDependsOnDataVars($dataVarConfig) as $dependsOnDataVarName) {
                try {
                    $dataVarValue = $this->dataVarsFinalValuesProvider->getDataVarFinalValue($dependsOnDataVarName);
                    $context[$dependsOnDataVarName] = $dataVarValue;
                } catch (\Exception|\Error $e) {
                    $context[$dependsOnDataVarName] = null;
                }
            }

            $value = $this->expressionLanguage->evaluate($dataVarConfig['value'], $context);
            if (is_string($value)) {
                $value = $this->matchDataVar($value);
            } elseif (is_array($value) || is_object($value)) {
                $value = $this->matchDataVarInArray($value);
            }

            return $value;
        } catch (\Exception|\Error $e) {
            return '';
        }
    }

    /**
     * @param string $dataVarExpression
     * @param bool $forDisplay
     * @return string
     */
    public function getNonEscapedDataVarValue($dataVarExpression, $forDisplay = true)
    {
        try {
            $data = $this->getDataVarValue($dataVarExpression);
            $attr = $data;
            if (is_object($data)) {
                $data = (array)$data;
            }
            if (is_array($data)) {
                $attr = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
            }
            if ($forDisplay === true && is_bool($attr)) {
                $attr = ($attr === true) ? 'true' : 'false';
            }

            return $attr;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param string $dataVarExpression
     * @return string
     */
    public function getEscapedDataVarValue($dataVarExpression)
    {
        try {
            $data = $this->getDataVarValue($dataVarExpression);
            $attr = $data;
            if (is_object($data)) {
                $data = (array)$data;
            }
            if (is_array($data)) {
                $attr = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
            }
            if (is_bool($attr)) {
                $attr = ($attr === true) ? 'true' : 'false';
            }

            return esc_html($attr);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @return string
     */
    public function getDynamicCssVarValue($name, $value)
    {
        if (in_array($value, [null, ''])) {
            return '';
        }

        return sprintf('%s: %s;', $name, $value);
    }

    /**
     * @param string $dataVarExpression
     * @return mixed
     * @throws \Exception
     */
    public function evaluateVisibilityCondition($dataVarExpression)
    {
        if (in_array($dataVarExpression, [null, ''])) {
            return true;
        }
        try {
            preg_match_all('/\[\[\[(.*?)\]\]\]/s', $dataVarExpression, $nonEscapedDataVars);
            $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[1]);

            foreach ($nonEscapedDataVarsNames as $nonEscapedDataVarName) {
                $countOpen = substr_count($nonEscapedDataVarName,'[');
                $countClose = substr_count($nonEscapedDataVarName,']');
                $diff = $countOpen - $countClose;
                if ($diff > 0) {
                    $prefix = ']';
                    for ($i = 1; $i < $diff; $i++) {
                        $prefix = $prefix . ']';
                    }
                    $nonEscapedDataVarName = $nonEscapedDataVarName . $prefix;
                }
                $dataVarExpression = str_replace(
                    sprintf("[[[%s]]]", $nonEscapedDataVarName),
                    sprintf("[^builderius_data_var('%s')|raw^]", str_replace("'", "\'", trim($nonEscapedDataVarName))),
                    $dataVarExpression
                );
            }
            preg_match_all('/\[\[(.*?)\]\]/s', $dataVarExpression, $escapedDataVars);
            $escapedDataVarsNames = array_unique($escapedDataVars[1]);

            foreach ($escapedDataVarsNames as $escapedDataVarName) {
                $countOpen = substr_count($escapedDataVarName,'[');
                $countClose = substr_count($escapedDataVarName,']');
                $diff = $countOpen - $countClose;
                if ($diff > 0) {
                    $prefix = ']';
                    for ($i = 1; $i < $diff; $i++) {
                        $prefix = $prefix . ']';
                    }
                    $escapedDataVarName = $escapedDataVarName . $prefix;
                }
                $dataVarExpression = str_replace(
                    sprintf("[[%s]]", $escapedDataVarName),
                    sprintf("[^builderius_data_var_escaped('%s')|raw^]", str_replace("'", "\'", trim($escapedDataVarName))),
                    $dataVarExpression
                );
            }
            $dataVarConfig = [
                'name' => 'no_cache',
                'type' => 'expression',
                'value' => $dataVarExpression
            ];
            $context = [];
            foreach ($this->getDependsOnDataVars($dataVarConfig) as $dependsOnDataVarName) {
                $dataVarValue = $this->dataVarsFinalValuesProvider->getDataVarFinalValue($dependsOnDataVarName);
                $context[$dependsOnDataVarName] = $dataVarValue;
            }

            $result = $this->expressionLanguage->evaluate($dataVarConfig['value'], $context);
            if ($result === false) {
                $gettingHookArgsBeforeHook = $this->cache->get('getting_hook_args_before_hook');
                if (true === $gettingHookArgsBeforeHook) {
                    return true;
                }
            }

            return $result == true;
        } catch (\Exception|\Error $e) {
            $gettingHookArgsBeforeHook = $this->cache->get('getting_hook_args_before_hook');
            if (true === $gettingHookArgsBeforeHook) {
                return true;
            }
            return false;
        }
    }

    /**
     * @param string $cssIdentivier
     * @param array $availableVisibilityConditions
     * @return string|null
     */
    public function getVisibilityConditionForCssIdentifier($cssIdentivier, array $availableVisibilityConditions)
    {
        foreach (array_keys($availableVisibilityConditions) as $id) {
            if (strpos($cssIdentivier, $id) !== false) {
                return $availableVisibilityConditions[$id];
            }
        }

        return null;
    }

    /**
     * @param $string
     */
    public function escapeQuotes($string)
    {
        return str_replace('"', '\"', $string);
    }

    /**
     * @inheritDoc
     */
    private function getDependsOnDataVars(array $dataVarConfig)
    {
        if ($dataVarConfig['name'] === 'no_cache') {
            $dependsOnDataVars = false;
        } else {
            $dependsOnDataVars = $this->cache->get(sprintf('%s_depends_on_data_vars', $dataVarConfig['name']));
        }
        if (false === $dependsOnDataVars) {
            $expression = $dataVarConfig['value'];
            $tokensStream = $this->lexer->tokenize($expression);
            $dependsOnDataVars = [];
            $prev = null;
            do {
                if (isset($current)) {
                    $prev = $current;
                }
                /** @var Token $current */
                $current = $tokensStream->current;
                /*if($current->type === 'string') {
                    $subTokensStream = $this->lexer->tokenize($current->value);
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
                }*/
                $tokensStream->next();
                $next = $tokensStream->current;
                if ($current->type === 'name' && $current->value !== 'true' && $next->value !== '(' && (!$prev || ($prev->type !== 'punctuation' || in_array($prev->value, ['(', ',', ':', ' '])))) {
                    if (!in_array($current->value, $dependsOnDataVars)) {
                        $dependsOnDataVars[] = $current->value;
                    }
                }
            } while (!$tokensStream->isEOF());
            if ($dataVarConfig['name'] !== 'no_cache') {
                $this->cache->set(sprintf('%s_depends_on_data_vars', $dataVarConfig['name']), $dependsOnDataVars);
            }
        }

        return $dependsOnDataVars;
    }

    /**
     * @param $value
     * @return string
     */
    protected function matchDataVar($value)
    {
        preg_match_all('/\[\[\[(.*?)\]\]\]/s', $value, $nonEscapedDataVars);
        $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[1]);

        foreach ($nonEscapedDataVarsNames as $nonEscapedDataVarName) {
            $countOpen = substr_count($nonEscapedDataVarName,'[');
            $countClose = substr_count($nonEscapedDataVarName,']');
            $diff = $countOpen - $countClose;
            if ($diff > 0) {
                $prefix = ']';
                for ($i = 1; $i < $diff; $i++) {
                    $prefix = $prefix . ']';
                }
                $nonEscapedDataVarName = $nonEscapedDataVarName . $prefix;
            }
            $value = str_replace(
                sprintf("[[[%s]]]", $nonEscapedDataVarName),
                $this->getNonEscapedDataVarValue(trim($nonEscapedDataVarName)),
                $value
            );
        }
        preg_match_all('/\[\[(.*?)\]\]/s', $value, $escapedDataVars);
        $escapedDataVarsNames = array_unique($escapedDataVars[1]);

        foreach ($escapedDataVarsNames as $escapedDataVarName) {
            $countOpen = substr_count($escapedDataVarName,'[');
            $countClose = substr_count($escapedDataVarName,']');
            $diff = $countOpen - $countClose;
            if ($diff > 0) {
                $prefix = ']';
                for ($i = 1; $i < $diff; $i++) {
                    $prefix = $prefix . ']';
                }
                $escapedDataVarName = $escapedDataVarName . $prefix;
            }
            $value = str_replace(
                sprintf("[[%s]]", $escapedDataVarName),
                $this->getEscapedDataVarValue(trim($escapedDataVarName)),
                $value
            );
        }

        return $value;
    }

    /**
     * @param array|object $value
     * @return array|object
     */
    protected function matchDataVarInArray($value)
    {
        foreach ((array)$value as $k => $v) {
            if (is_string($v)) {
                if (is_array($value)) {
                    $value[$k] = $this->matchDataVar($v);
                } else {
                    $value->$k = $this->matchDataVar($v);
                }
            } elseif (is_array($v) || is_object($v)) {
                if (is_array($value)) {
                    $value[$k] = $this->matchDataVarInArray($v);
                } else {
                    $value->$k = $this->matchDataVarInArray($v);
                }
            }
        }

        return $value;
    }
}