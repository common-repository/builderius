<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Utils\Utils;
use function is_bool;
class BooleanType extends \Builderius\GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = \Builderius\GraphQL\Type\Definition\Type::BOOLEAN;
    /** @var string */
    public $description = 'The `Boolean` scalar type represents `true` or `false`.';
    /**
     * Serialize the given value to a boolean.
     *
     * The GraphQL spec leaves this up to the implementations, so we just do what
     * PHP does natively to make this intuitive for developers.
     *
     * @param mixed $value
     */
    public function serialize($value) : bool
    {
        return (bool) $value;
    }
    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        if (\is_bool($value)) {
            return $value;
        }
        throw new \Builderius\GraphQL\Error\Error('Boolean cannot represent a non boolean value: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
    }
    /**
     * @param mixed[]|null $variables
     *
     * @throws Exception
     */
    public function parseLiteral(\Builderius\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
        if (!$valueNode instanceof \Builderius\GraphQL\Language\AST\BooleanValueNode) {
            // Intentionally without message, as all information already in wrapped Exception
            throw new \Builderius\GraphQL\Error\Error();
        }
        return $valueNode->value;
    }
}
