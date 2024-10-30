<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Exception;
use Builderius\GraphQL\Error\Error;

class ArrayOrDataVarType extends ScalarType
{
    /**
     * {@inheritdoc}
     */
    public $name = 'ArrayOrDataVar';

    /**
     * {@inheritdoc}
     */
    public $description = 'array|"{{localDataVar}}"|"[[globalDataVar]]"';

    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $result = is_array($value) ?
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
        $result = is_array($value) ?
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
        } elseif ($valueNode instanceof ListValueNode) {
            $value = [];
            foreach ($valueNode->values as $node) {
                $value[] = $node->value;
            }

            return $value;
        }
        // Intentionally without message, as all information already in wrapped Exception
        throw new Error();
    }
}
