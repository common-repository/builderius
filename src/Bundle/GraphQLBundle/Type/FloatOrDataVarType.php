<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Utils\Utils;
use Exception;
use Builderius\GraphQL\Error\Error;

class FloatOrDataVarType extends ScalarType
{
    /**
     * {@inheritdoc}
     */
    public $name = 'FloatOrDataVar';

    /**
     * {@inheritdoc}
     */
    public $description = 'int|float|"{{localDataVar}}"|"[[globalDataVar]]"';

    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $result = is_float($value) || is_int($value) ?
            (float) $value :
            (
            (
                (is_string($value) && strpos($value, '[[') !== false && strpos($value, ']]') !== false) ||
                (is_string($value) && strpos($value, '{{') !== false && strpos($value, '}}') !== false)
            ) ?
                $value :
                null
            );

        return $result;
    }
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function parseValue($value) : float
    {
        $result = is_float($value) || is_int($value) ?
            (float) $value :
            (
                (
                    (is_string($value) && strpos($value, '[[') !== false && strpos($value, ']]') !== false) ||
                    (is_string($value) && strpos($value, '{{') !== false && strpos($value, '}}') !== false)
                ) ?
                $value :
                null
            );

        return $result;
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof FloatValueNode || $valueNode instanceof IntValueNode) {
            return (float) $valueNode->value;
        }
        if ($valueNode instanceof StringValueNode) {
            $number = filter_var($valueNode->value, FILTER_VALIDATE_FLOAT);
            if ($number !== false) {
                return (float)$valueNode->value;
            } elseif ((strpos($valueNode->value, '[[') !== false && strpos($valueNode->value, ']]') !== false)) {
                return $valueNode->value;
            } elseif ((strpos($valueNode->value, '{{') !== false && strpos($valueNode->value, '}}') !== false)) {
                return $valueNode->value;
            } elseif ($valueNode->value === '') {
                return null;
            }
        }
        // Intentionally without message, as all information already in wrapped Exception
        throw new Error();
    }
}
