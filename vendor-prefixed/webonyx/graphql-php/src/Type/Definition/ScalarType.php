<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode;
use Builderius\GraphQL\Utils\Utils;
use function is_string;
/**
 * Scalar Type Definition
 *
 * The leaf values of any request and input values to arguments are
 * Scalars (or Enums) and are defined with a name and a series of coercion
 * functions used to ensure validity.
 *
 * Example:
 *
 * class OddType extends ScalarType
 * {
 *     public $name = 'Odd',
 *     public function serialize($value)
 *     {
 *         return $value % 2 === 1 ? $value : null;
 *     }
 * }
 */
abstract class ScalarType extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\OutputType, \Builderius\GraphQL\Type\Definition\InputType, \Builderius\GraphQL\Type\Definition\LeafType, \Builderius\GraphQL\Type\Definition\NullableType, \Builderius\GraphQL\Type\Definition\NamedType
{
    /** @var ScalarTypeDefinitionNode|null */
    public $astNode;
    /** @var ScalarTypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config = [])
    {
        $this->name = $config['name'] ?? $this->tryInferName();
        $this->description = $config['description'] ?? $this->description;
        $this->astNode = $config['astNode'] ?? null;
        $this->extensionASTNodes = $config['extensionASTNodes'] ?? null;
        $this->config = $config;
        \Builderius\GraphQL\Utils\Utils::invariant(\is_string($this->name), 'Must provide name.');
    }
}
