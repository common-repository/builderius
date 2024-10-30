<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode;
use Builderius\GraphQL\Utils\Utils;
use function is_callable;
use function is_string;
use function sprintf;
class InterfaceType extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\AbstractType, \Builderius\GraphQL\Type\Definition\OutputType, \Builderius\GraphQL\Type\Definition\CompositeType, \Builderius\GraphQL\Type\Definition\NullableType, \Builderius\GraphQL\Type\Definition\NamedType
{
    /** @var InterfaceTypeDefinitionNode|null */
    public $astNode;
    /** @var InterfaceTypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * Lazily initialized.
     *
     * @var FieldDefinition[]
     */
    private $fields;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        if (!isset($config['name'])) {
            $config['name'] = $this->tryInferName();
        }
        \Builderius\GraphQL\Utils\Utils::invariant(\is_string($config['name']), 'Must provide name.');
        $this->name = $config['name'];
        $this->description = $config['description'] ?? null;
        $this->astNode = $config['astNode'] ?? null;
        $this->extensionASTNodes = $config['extensionASTNodes'] ?? null;
        $this->config = $config;
    }
    /**
     * @param mixed $type
     *
     * @return $this
     *
     * @throws InvariantViolation
     */
    public static function assertInterfaceType($type) : self
    {
        \Builderius\GraphQL\Utils\Utils::invariant($type instanceof self, 'Expected ' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . ' to be a GraphQL Interface type.');
        return $type;
    }
    public function getField(string $name) : \Builderius\GraphQL\Type\Definition\FieldDefinition
    {
        if (!isset($this->fields)) {
            $this->initializeFields();
        }
        \Builderius\GraphQL\Utils\Utils::invariant(isset($this->fields[$name]), 'Field "%s" is not defined for type "%s"', $name, $this->name);
        return $this->fields[$name];
    }
    public function hasField(string $name) : bool
    {
        if (!isset($this->fields)) {
            $this->initializeFields();
        }
        return isset($this->fields[$name]);
    }
    /**
     * @return FieldDefinition[]
     */
    public function getFields() : array
    {
        if (!isset($this->fields)) {
            $this->initializeFields();
        }
        return $this->fields;
    }
    protected function initializeFields() : void
    {
        $fields = $this->config['fields'] ?? [];
        $this->fields = \Builderius\GraphQL\Type\Definition\FieldDefinition::defineFieldMap($this, $fields);
    }
    /**
     * Resolves concrete ObjectType for given object value
     *
     * @param object $objectValue
     * @param mixed  $context
     *
     * @return Type|null
     */
    public function resolveType($objectValue, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info)
    {
        if (isset($this->config['resolveType'])) {
            $fn = $this->config['resolveType'];
            return $fn($objectValue, $context, $info);
        }
        return null;
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
        parent::assertValid();
        $resolveType = $this->config['resolveType'] ?? null;
        \Builderius\GraphQL\Utils\Utils::invariant(!isset($resolveType) || \is_callable($resolveType), \sprintf('%s must provide "resolveType" as a function, but got: %s', $this->name, \Builderius\GraphQL\Utils\Utils::printSafe($resolveType)));
    }
}
