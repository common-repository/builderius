<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
/*
export type GraphQLLeafType =
GraphQLScalarType |
GraphQLEnumType;
*/
interface LeafType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function serialize($value);
    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function parseValue($value);
    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|NullValueNode $valueNode
     * @param mixed[]|null                                                               $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral(\Builderius\GraphQL\Language\AST\Node $valueNode, ?array $variables = null);
}
