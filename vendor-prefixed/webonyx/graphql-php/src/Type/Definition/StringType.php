<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Utils\Utils;
use function is_object;
use function is_scalar;
use function is_string;
use function method_exists;
class StringType extends \Builderius\GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = \Builderius\GraphQL\Type\Definition\Type::STRING;
    /** @var string */
    public $description = 'The `String` scalar type represents textual data, represented as UTF-8
character sequences. The String type is most often used by GraphQL to
represent free-form human-readable text.';
    /**
     * @param mixed $value
     *
     * @return mixed|string
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $canCast = \is_scalar($value) || \is_object($value) && \method_exists($value, '__toString') || $value === null;
        if (!$canCast) {
            throw new \Builderius\GraphQL\Error\Error('String cannot represent value: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
        }
        return (string) $value;
    }
    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        if (!\is_string($value)) {
            throw new \Builderius\GraphQL\Error\Error('String cannot represent a non string value: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
        }
        return $value;
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return string
     *
     * @throws Exception
     */
    public function parseLiteral(\Builderius\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof \Builderius\GraphQL\Language\AST\StringValueNode) {
            return $valueNode->value;
        }
        // Intentionally without message, as all information already in wrapped Exception
        throw new \Builderius\GraphQL\Error\Error();
    }
}
