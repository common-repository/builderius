<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\TypeDefinitionNode;
use Builderius\GraphQL\Language\AST\TypeExtensionNode;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Utils\Utils;
use JsonSerializable;
use ReflectionClass;
use function array_keys;
use function array_merge;
use function assert;
use function implode;
use function in_array;
use function preg_replace;
use function trigger_error;
use const E_USER_DEPRECATED;
/**
 * Registry of standard GraphQL types
 * and a base class for all other types.
 */
abstract class Type implements \JsonSerializable
{
    public const STRING = 'String';
    public const INT = 'Int';
    public const BOOLEAN = 'Boolean';
    public const FLOAT = 'Float';
    public const ID = 'ID';
    /** @var array<string, ScalarType> */
    protected static $standardTypes;
    /** @var Type[] */
    private static $builtInTypes;
    /** @var string */
    public $name;
    /** @var string|null */
    public $description;
    /** @var TypeDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /** @var TypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @api
     */
    public static function id() : \Builderius\GraphQL\Type\Definition\ScalarType
    {
        if (!isset(static::$standardTypes[self::ID])) {
            static::$standardTypes[self::ID] = new \Builderius\GraphQL\Type\Definition\IDType();
        }
        return static::$standardTypes[self::ID];
    }
    /**
     * @api
     */
    public static function string() : \Builderius\GraphQL\Type\Definition\ScalarType
    {
        if (!isset(static::$standardTypes[self::STRING])) {
            static::$standardTypes[self::STRING] = new \Builderius\GraphQL\Type\Definition\StringType();
        }
        return static::$standardTypes[self::STRING];
    }
    /**
     * @api
     */
    public static function boolean() : \Builderius\GraphQL\Type\Definition\ScalarType
    {
        if (!isset(static::$standardTypes[self::BOOLEAN])) {
            static::$standardTypes[self::BOOLEAN] = new \Builderius\GraphQL\Type\Definition\BooleanType();
        }
        return static::$standardTypes[self::BOOLEAN];
    }
    /**
     * @api
     */
    public static function int() : \Builderius\GraphQL\Type\Definition\ScalarType
    {
        if (!isset(static::$standardTypes[self::INT])) {
            static::$standardTypes[self::INT] = new \Builderius\GraphQL\Type\Definition\IntType();
        }
        return static::$standardTypes[self::INT];
    }
    /**
     * @api
     */
    public static function float() : \Builderius\GraphQL\Type\Definition\ScalarType
    {
        if (!isset(static::$standardTypes[self::FLOAT])) {
            static::$standardTypes[self::FLOAT] = new \Builderius\GraphQL\Type\Definition\FloatType();
        }
        return static::$standardTypes[self::FLOAT];
    }
    /**
     * @api
     */
    public static function listOf(\Builderius\GraphQL\Type\Definition\Type $wrappedType) : \Builderius\GraphQL\Type\Definition\ListOfType
    {
        return new \Builderius\GraphQL\Type\Definition\ListOfType($wrappedType);
    }
    /**
     * @param callable|NullableType $wrappedType
     *
     * @api
     */
    public static function nonNull($wrappedType) : \Builderius\GraphQL\Type\Definition\NonNull
    {
        return new \Builderius\GraphQL\Type\Definition\NonNull($wrappedType);
    }
    /**
     * Checks if the type is a builtin type
     */
    public static function isBuiltInType(\Builderius\GraphQL\Type\Definition\Type $type) : bool
    {
        return \in_array($type->name, \array_keys(self::getAllBuiltInTypes()), \true);
    }
    /**
     * Returns all builtin in types including base scalar and
     * introspection types
     *
     * @return Type[]
     */
    public static function getAllBuiltInTypes()
    {
        if (self::$builtInTypes === null) {
            self::$builtInTypes = \array_merge(\Builderius\GraphQL\Type\Introspection::getTypes(), self::getStandardTypes());
        }
        return self::$builtInTypes;
    }
    /**
     * Returns all builtin scalar types
     *
     * @return ScalarType[]
     */
    public static function getStandardTypes()
    {
        return [self::ID => static::id(), self::STRING => static::string(), self::FLOAT => static::float(), self::INT => static::int(), self::BOOLEAN => static::boolean()];
    }
    /**
     * @deprecated Use method getStandardTypes() instead
     *
     * @return Type[]
     *
     * @codeCoverageIgnore
     */
    public static function getInternalTypes()
    {
        \trigger_error(__METHOD__ . ' is deprecated. Use Type::getStandardTypes() instead', \E_USER_DEPRECATED);
        return self::getStandardTypes();
    }
    /**
     * @param array<string, ScalarType> $types
     */
    public static function overrideStandardTypes(array $types)
    {
        $standardTypes = self::getStandardTypes();
        foreach ($types as $type) {
            \Builderius\GraphQL\Utils\Utils::invariant($type instanceof \Builderius\GraphQL\Type\Definition\Type, 'Expecting instance of %s, got %s', self::class, \Builderius\GraphQL\Utils\Utils::printSafe($type));
            \Builderius\GraphQL\Utils\Utils::invariant(isset($type->name, $standardTypes[$type->name]), 'Expecting one of the following names for a standard type: %s, got %s', \implode(', ', \array_keys($standardTypes)), \Builderius\GraphQL\Utils\Utils::printSafe($type->name ?? null));
            static::$standardTypes[$type->name] = $type;
        }
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isInputType($type) : bool
    {
        return self::getNamedType($type) instanceof \Builderius\GraphQL\Type\Definition\InputType;
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function getNamedType($type) : ?\Builderius\GraphQL\Type\Definition\Type
    {
        if ($type === null) {
            return null;
        }
        while ($type instanceof \Builderius\GraphQL\Type\Definition\WrappingType) {
            $type = $type->getWrappedType();
        }
        return $type;
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isOutputType($type) : bool
    {
        return self::getNamedType($type) instanceof \Builderius\GraphQL\Type\Definition\OutputType;
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isLeafType($type) : bool
    {
        return $type instanceof \Builderius\GraphQL\Type\Definition\LeafType;
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isCompositeType($type) : bool
    {
        return $type instanceof \Builderius\GraphQL\Type\Definition\CompositeType;
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isAbstractType($type) : bool
    {
        return $type instanceof \Builderius\GraphQL\Type\Definition\AbstractType;
    }
    /**
     * @param mixed $type
     */
    public static function assertType($type) : \Builderius\GraphQL\Type\Definition\Type
    {
        \assert($type instanceof \Builderius\GraphQL\Type\Definition\Type, new \Builderius\GraphQL\Error\InvariantViolation('Expected ' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . ' to be a GraphQL type.'));
        return $type;
    }
    /**
     * @api
     */
    public static function getNullableType(\Builderius\GraphQL\Type\Definition\Type $type) : \Builderius\GraphQL\Type\Definition\Type
    {
        return $type instanceof \Builderius\GraphQL\Type\Definition\NonNull ? $type->getWrappedType() : $type;
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid()
    {
        \Builderius\GraphQL\Utils\Utils::assertValidName($this->name);
    }
    /**
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toString();
    }
    /**
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
    /**
     * @return string|null
     */
    protected function tryInferName()
    {
        if ($this->name) {
            return $this->name;
        }
        // If class is extended - infer name from className
        // QueryType -> Type
        // SomeOtherType -> SomeOther
        $tmp = new \ReflectionClass($this);
        $name = $tmp->getShortName();
        if ($tmp->getNamespaceName() !== __NAMESPACE__) {
            return \preg_replace('~Type$~', '', $name);
        }
        return null;
    }
}
