<?php


namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\GraphQLBundle\Helper\GraphQLHelper;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractLocalVarsAwareResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var GraphQLObjectCache
     */
    protected $graphqlSubfieldsCache;

    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @param GraphQLObjectCache $graphqlSubfieldsCache
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(
        GraphQLObjectCache $graphqlSubfieldsCache,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->graphqlSubfieldsCache = $graphqlSubfieldsCache;
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @param array $args
     * @param array $path
     * @return array
     */
    protected function processArguments(array $args, array $path)
    {
        if (!empty($args)) {
            $res = $this->graphqlSubfieldsCache->get('results');
            if (false !== $res) {
                array_pop($path);
                if (!empty($path)) {
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    $context = $propertyAccessor->getValue($res, sprintf('[%s]', implode('][', $path)));
                } else {
                    $context = $res;
                }
                $args = GraphQLHelper::array_map_deep($args, [$this, 'processLocalVars'], $context);
            }
        }

        return $args;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return array|mixed|string|string[]
     */
    public function processLocalVars($value, $context)
    {
        if (is_string($value)) {
            preg_match_all('/\{\{\{(.*?)\}\}\}/s', $value, $nonEscapedExpressions);
            foreach ($nonEscapedExpressions[1] as $nonEscapedExpression) {
                if (is_string($nonEscapedExpression)) {
                    try {
                        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
                        $resolved = $this->expressionLanguage->evaluate($nonEscapedExpression, $context);
                        restore_error_handler();
                    } catch (\Exception $e) {
                        $resolved = null;
                    }
                    if (is_array($resolved) || is_object($resolved) || is_resource($resolved)) {
                        $value = $resolved;
                    } else {
                        $value = str_replace(
                            sprintf("{{{%s}}}", $nonEscapedExpression),
                            $resolved,
                            $value
                        );
                        if ((string)$resolved == $value) {
                            $value = $resolved;
                        }
                    }
                }
            }
        }
        if (is_string($value)) {
            preg_match_all('/\{\{(.*?)\}\}/s', $value, $escapedExpressions);
            foreach ($escapedExpressions[1] as $escapedExpression) {
                if (is_string($escapedExpression)) {
                    try {
                        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
                        $resolved = $this->expressionLanguage->evaluate($escapedExpression, $context);
                        restore_error_handler();
                    } catch (\Throwable $e) {
                        $resolved = null;
                    }
                    if (is_array($resolved) || is_object($resolved) || is_resource($resolved)) {
                        $value = $resolved;
                    } else {
                        $value = str_replace(
                            sprintf("{{%s}}", $escapedExpression),
                            $resolved,
                            $value
                        );
                        if ((string)$resolved == $value) {
                            $value = $resolved;
                        }
                    }
                }
            }
        }

        return $value;
    }
}