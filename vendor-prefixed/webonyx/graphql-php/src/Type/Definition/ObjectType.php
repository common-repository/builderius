<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Deferred;
use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\Utils;
use function array_map;
use function is_array;
use function is_callable;
use function is_string;
use function sprintf;
/**
 * Object Type Definition
 *
 * Almost all of the GraphQL types you define will be object types. Object types
 * have a name, but most importantly describe their fields.
 *
 * Example:
 *
 *     $AddressType = new ObjectType([
 *       'name' => 'Address',
 *       'fields' => [
 *         'street' => [ 'type' => GraphQL\Type\Definition\Type::string() ],
 *         'number' => [ 'type' => GraphQL\Type\Definition\Type::int() ],
 *         'formatted' => [
 *           'type' => GraphQL\Type\Definition\Type::string(),
 *           'resolve' => function($obj) {
 *             return $obj->number . ' ' . $obj->street;
 *           }
 *         ]
 *       ]
 *     ]);
 *
 * When two types need to refer to each other, or a type needs to refer to
 * itself in a field, you can use a function expression (aka a closure or a
 * thunk) to supply the fields lazily.
 *
 * Example:
 *
 *     $PersonType = null;
 *     $PersonType = new ObjectType([
 *       'name' => 'Person',
 *       'fields' => function() use (&$PersonType) {
 *          return [
 *              'name' => ['type' => GraphQL\Type\Definition\Type::string() ],
 *              'bestFriend' => [ 'type' => $PersonType ],
 *          ];
 *        }
 *     ]);
 */
class ObjectType extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\OutputType, \Builderius\GraphQL\Type\Definition\CompositeType, \Builderius\GraphQL\Type\Definition\NullableType, \Builderius\GraphQL\Type\Definition\NamedType
{
    /** @var ObjectTypeDefinitionNode|null */
    public $astNode;
    /** @var ObjectTypeExtensionNode[] */
    public $extensionASTNodes;
    /** @var ?callable */
    public $resolveFieldFn;
    /**
     * Lazily initialized.
     *
     * @var FieldDefinition[]
     */
    private $fields;
    /**
     * Lazily initialized.
     *
     * @var InterfaceType[]
     */
    private $interfaces;
    /**
     * Lazily initialized.
     *
     * @var InterfaceType[]
     */
    private $interfaceMap;
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
        $this->resolveFieldFn = $config['resolveField'] ?? null;
        $this->astNode = $config['astNode'] ?? null;
        $this->extensionASTNodes = $config['extensionASTNodes'] ?? [];
        $this->config = $config;
    }
    /**
     * @param mixed $type
     *
     * @return $this
     *
     * @throws InvariantViolation
     */
    public static function assertObjectType($type) : self
    {
        \Builderius\GraphQL\Utils\Utils::invariant($type instanceof self, 'Expected ' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . ' to be a GraphQL Object type.');
        return $type;
    }
    /**
     * @throws InvariantViolation
     */
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
     *
     * @throws InvariantViolation
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
    public function implementsInterface(\Builderius\GraphQL\Type\Definition\InterfaceType $interfaceType) : bool
    {
        if (!isset($this->interfaceMap)) {
            $this->interfaceMap = [];
            foreach ($this->getInterfaces() as $interface) {
                /** @var Type&InterfaceType $interface */
                $interface = \Builderius\GraphQL\Type\Schema::resolveType($interface);
                $this->interfaceMap[$interface->name] = $interface;
            }
        }
        return isset($this->interfaceMap[$interfaceType->name]);
    }
    /**
     * @return InterfaceType[]
     */
    public function getInterfaces() : array
    {
        if (!isset($this->interfaces)) {
            $interfaces = $this->config['interfaces'] ?? [];
            if (\is_callable($interfaces)) {
                $interfaces = $interfaces();
            }
            if ($interfaces !== null && !\is_array($interfaces)) {
                throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('%s interfaces must be an Array or a callable which returns an Array.', $this->name));
            }
            /** @var InterfaceType[] $interfaces */
            $interfaces = \array_map([\Builderius\GraphQL\Type\Schema::class, 'resolveType'], $interfaces ?? []);
            $this->interfaces = $interfaces;
        }
        return $this->interfaces;
    }
    /**
     * @param mixed $value
     * @param mixed $context
     *
     * @return bool|Deferred|null
     */
    public function isTypeOf($value, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info)
    {
        return isset($this->config['isTypeOf']) ? $this->config['isTypeOf']($value, $context, $info) : null;
    }
    /**
     * Validates type config and throws if one of type options is invalid.
     * Note: this method is shallow, it won't validate object fields and their arguments.
     *
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
        parent::assertValid();
        \Builderius\GraphQL\Utils\Utils::invariant($this->description === null || \is_string($this->description), \sprintf('%s description must be string if set, but it is: %s', $this->name, \Builderius\GraphQL\Utils\Utils::printSafe($this->description)));
        $isTypeOf = $this->config['isTypeOf'] ?? null;
        \Builderius\GraphQL\Utils\Utils::invariant($isTypeOf === null || \is_callable($isTypeOf), \sprintf('%s must provide "isTypeOf" as a function, but got: %s', $this->name, \Builderius\GraphQL\Utils\Utils::printSafe($isTypeOf)));
        foreach ($this->getFields() as $field) {
            $field->assertValid($this);
        }
    }
}
