<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Utils\Utils;
use function floatval;
use function is_bool;
use function is_finite;
use function is_float;
use function is_int;
use function is_numeric;
class FloatType extends \Builderius\GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = \Builderius\GraphQL\Type\Definition\Type::FLOAT;
    /** @var string */
    public $description = 'The `Float` scalar type represents signed double-precision fractional
values as specified by
[IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point). ';
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value) : float
    {
        $float = \is_numeric($value) || \is_bool($value) ? (float) $value : null;
        if ($float === null || !\is_finite($float)) {
            throw new \Builderius\GraphQL\Error\Error('Float cannot represent non numeric value: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
        }
        return $float;
    }
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function parseValue($value) : float
    {
        $float = \is_float($value) || \is_int($value) ? (float) $value : null;
        if ($float === null || !\is_finite($float)) {
            throw new \Builderius\GraphQL\Error\Error('Float cannot represent non numeric value: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
        }
        return $float;
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return float
     *
     * @throws Exception
     */
    public function parseLiteral(\Builderius\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof \Builderius\GraphQL\Language\AST\FloatValueNode || $valueNode instanceof \Builderius\GraphQL\Language\AST\IntValueNode) {
            return (float) $valueNode->value;
        }
        // Intentionally without message, as all information already in wrapped Exception
        throw new \Builderius\GraphQL\Error\Error();
    }
}
