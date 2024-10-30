<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Utils\Utils;
use Exception;

class DataVarType extends ScalarType
{
    /**
     * {@inheritdoc}
     */
    public $name = 'DataVar';

    /**
     * {@inheritdoc}
     */
    public $description = '"{{localDataVar}}"|"[[globalDataVar]]"';

    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $result = is_string($value) && (
            (strpos($value, '[[') !== false && strpos($value, ']]')) ||
            (strpos($value, '{{') !== false && strpos($value, '}}'))
        ) !== false ?
            $value :
            null;
        if ($result === null) {
            throw new Error('DataVar cannot represent non enclosed in {{}} or [[]] string value: ' . Utils::printSafe($value));
        }
        return $result;
    }
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function parseValue($value) : float
    {
        $result = is_string($value) && (
            (strpos($value, '[[') !== false && strpos($value, ']]')) ||
            (strpos($value, '{{') !== false && strpos($value, '}}'))
        ) !== false ?
            $value :
            null;
        if ($result === null) {
            throw new Error('DataVar cannot represent non enclosed in {{}} or [[]] string value: ' . Utils::printSafe($value));
        }
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
            if ((strpos($valueNode->value, '[[') !== false && strpos($valueNode->value, ']]') !== false)) {
                return $valueNode->value;
            } elseif ((strpos($valueNode->value, '{{') !== false && strpos($valueNode->value, '}}') !== false)) {
                return $valueNode->value;
            }
        }
        // Intentionally without message, as all information already in wrapped Exception
        throw new Error();
    }
}
