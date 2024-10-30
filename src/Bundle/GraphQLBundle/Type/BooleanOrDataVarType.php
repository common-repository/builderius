<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Exception;

class BooleanOrDataVarType extends ScalarType
{
    /**
     * {@inheritdoc}
     */
    public $name = 'BooleanOrDataVar';

    /**
     * {@inheritdoc}
     */
    public $description = 'bool|"{{localDataVar}}"|"[[globalDataVar]]"';

    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $result = is_bool($value) ?
            $value :
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
        $result = is_bool($value) ?
            $value :
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
        if ($valueNode instanceof BooleanValueNode) {
            return (bool)$valueNode->value;
        }
        if ($valueNode instanceof StringValueNode) {
            $bool = filter_var($valueNode->value, FILTER_VALIDATE_BOOLEAN);
            if ($bool !== false) {
                return (bool)$valueNode->value;
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
