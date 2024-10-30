<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Utils\Utils;
use Exception;
use Builderius\GraphQL\Error\Error;

class IntOrDataVarType extends ScalarType
{
    private const MAX_INT = 2147483647;
    private const MIN_INT = -2147483648;

    /**
     * {@inheritdoc}
     */
    public $name = 'IntOrDataVar';

    /**
     * {@inheritdoc}
     */
    public $description = 'int|"{{localDataVar}}"|"[[globalDataVar]]"';

    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $result = is_int($value) && $value <= self::MAX_INT && $value >= self::MIN_INT ?
            $value :
            (
                is_string($value) && (
                    (strpos($value, '[[') !== false && strpos($value, ']]')) ||
                    (strpos($value, '{{') !== false && strpos($value, '}}'))
                ) !== false ?
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
        $result = is_int($value) && $value <= self::MAX_INT && $value >= self::MIN_INT ?
            $value :
            (
                is_string($value) && (
                    (strpos($value, '[[') !== false && strpos($value, ']]')) ||
                    (strpos($value, '{{') !== false && strpos($value, '}}'))
                ) !== false ?
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
        if ($valueNode instanceof IntValueNode) {
            $val = (int) $valueNode->value;
            if ($valueNode->value === (string) $val && self::MIN_INT <= $val && $val <= self::MAX_INT) {
                return $val;
            }
        }
        if ($valueNode instanceof StringValueNode) {
            $number = filter_var($valueNode->value, FILTER_VALIDATE_INT);
            if ($number !== false) {
                return (int)$valueNode->value;
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
