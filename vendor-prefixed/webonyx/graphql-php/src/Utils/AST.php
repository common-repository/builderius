<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Utils;

use ArrayAccess;
use Exception;
use Builderius\GraphQL\Error\DebugFlag;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\ListTypeNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\Location;
use Builderius\GraphQL\Language\AST\NamedTypeNode;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NodeList;
use Builderius\GraphQL\Language\AST\NonNullTypeNode;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\ObjectFieldNode;
use Builderius\GraphQL\Language\AST\ObjectValueNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Language\AST\ValueNode;
use Builderius\GraphQL\Language\AST\VariableNode;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\IDType;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
use stdClass;
use Throwable;
use Traversable;
use function array_combine;
use function array_key_exists;
use function array_map;
use function count;
use function floatval;
use function intval;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function iterator_to_array;
use function property_exists;
/**
 * Various utilities dealing with AST
 */
class AST
{
    /**
     * Convert representation of AST as an associative array to instance of GraphQL\Language\AST\Node.
     *
     * For example:
     *
     * ```php
     * AST::fromArray([
     *     'kind' => 'ListValue',
     *     'values' => [
     *         ['kind' => 'StringValue', 'value' => 'my str'],
     *         ['kind' => 'StringValue', 'value' => 'my other str']
     *     ],
     *     'loc' => ['start' => 21, 'end' => 25]
     * ]);
     * ```
     *
     * Will produce instance of `ListValueNode` where `values` prop is a lazily-evaluated `NodeList`
     * returning instances of `StringValueNode` on access.
     *
     * This is a reverse operation for AST::toArray($node)
     *
     * @param mixed[] $node
     *
     * @api
     */
    public static function fromArray(array $node) : \Builderius\GraphQL\Language\AST\Node
    {
        if (!isset($node['kind']) || !isset(\Builderius\GraphQL\Language\AST\NodeKind::$classMap[$node['kind']])) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Unexpected node structure: ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($node));
        }
        $kind = $node['kind'] ?? null;
        $class = \Builderius\GraphQL\Language\AST\NodeKind::$classMap[$kind];
        $instance = new $class([]);
        if (isset($node['loc'], $node['loc']['start'], $node['loc']['end'])) {
            $instance->loc = \Builderius\GraphQL\Language\AST\Location::create($node['loc']['start'], $node['loc']['end']);
        }
        foreach ($node as $key => $value) {
            if ($key === 'loc' || $key === 'kind') {
                continue;
            }
            if (\is_array($value)) {
                if (isset($value[0]) || \count($value) === 0) {
                    $value = new \Builderius\GraphQL\Language\AST\NodeList($value);
                } else {
                    $value = self::fromArray($value);
                }
            }
            $instance->{$key} = $value;
        }
        return $instance;
    }
    /**
     * Convert AST node to serializable array
     *
     * @return mixed[]
     *
     * @api
     */
    public static function toArray(\Builderius\GraphQL\Language\AST\Node $node) : array
    {
        return $node->toArray(\true);
    }
    /**
     * Produces a GraphQL Value AST given a PHP value.
     *
     * Optionally, a GraphQL type may be provided, which will be used to
     * disambiguate between value primitives.
     *
     * | PHP Value     | GraphQL Value        |
     * | ------------- | -------------------- |
     * | Object        | Input Object         |
     * | Assoc Array   | Input Object         |
     * | Array         | List                 |
     * | Boolean       | Boolean              |
     * | String        | String / Enum Value  |
     * | Int           | Int                  |
     * | Float         | Int / Float          |
     * | Mixed         | Enum Value           |
     * | null          | NullValue            |
     *
     * @param Type|mixed|null $value
     *
     * @return ObjectValueNode|ListValueNode|BooleanValueNode|IntValueNode|FloatValueNode|EnumValueNode|StringValueNode|NullValueNode|null
     *
     * @api
     */
    public static function astFromValue($value, \Builderius\GraphQL\Type\Definition\InputType $type)
    {
        if ($type instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            $astValue = self::astFromValue($value, $type->getWrappedType());
            if ($astValue instanceof \Builderius\GraphQL\Language\AST\NullValueNode) {
                return null;
            }
            return $astValue;
        }
        if ($value === null) {
            return new \Builderius\GraphQL\Language\AST\NullValueNode([]);
        }
        // Convert PHP array to GraphQL list. If the GraphQLType is a list, but
        // the value is not an array, convert the value using the list's item type.
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            $itemType = $type->getWrappedType();
            if (\is_array($value) || $value instanceof \Traversable) {
                $valuesNodes = [];
                foreach ($value as $item) {
                    $itemNode = self::astFromValue($item, $itemType);
                    if (!$itemNode) {
                        continue;
                    }
                    $valuesNodes[] = $itemNode;
                }
                return new \Builderius\GraphQL\Language\AST\ListValueNode(['values' => new \Builderius\GraphQL\Language\AST\NodeList($valuesNodes)]);
            }
            return self::astFromValue($value, $itemType);
        }
        // Populate the fields of the input object by creating ASTs from each value
        // in the PHP object according to the fields in the input type.
        if ($type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
            $isArray = \is_array($value);
            $isArrayLike = $isArray || $value instanceof \ArrayAccess;
            if ($value === null || !$isArrayLike && !\is_object($value)) {
                return null;
            }
            $fields = $type->getFields();
            $fieldNodes = [];
            foreach ($fields as $fieldName => $field) {
                if ($isArrayLike) {
                    $fieldValue = $value[$fieldName] ?? null;
                } else {
                    $fieldValue = $value->{$fieldName} ?? null;
                }
                // Have to check additionally if key exists, since we differentiate between
                // "no key" and "value is null":
                if ($fieldValue !== null) {
                    $fieldExists = \true;
                } elseif ($isArray) {
                    $fieldExists = \array_key_exists($fieldName, $value);
                } elseif ($isArrayLike) {
                    $fieldExists = $value->offsetExists($fieldName);
                } else {
                    $fieldExists = \property_exists($value, $fieldName);
                }
                if (!$fieldExists) {
                    continue;
                }
                $fieldNode = self::astFromValue($fieldValue, $field->getType());
                if (!$fieldNode) {
                    continue;
                }
                $fieldNodes[] = new \Builderius\GraphQL\Language\AST\ObjectFieldNode(['name' => new \Builderius\GraphQL\Language\AST\NameNode(['value' => $fieldName]), 'value' => $fieldNode]);
            }
            return new \Builderius\GraphQL\Language\AST\ObjectValueNode(['fields' => new \Builderius\GraphQL\Language\AST\NodeList($fieldNodes)]);
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ScalarType || $type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
            // Since value is an internally represented value, it must be serialized
            // to an externally represented value before converting into an AST.
            try {
                $serialized = $type->serialize($value);
            } catch (\Throwable $error) {
                if ($error instanceof \Builderius\GraphQL\Error\Error && $type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                    return null;
                }
                throw $error;
            }
            // Others serialize based on their corresponding PHP scalar types.
            if (\is_bool($serialized)) {
                return new \Builderius\GraphQL\Language\AST\BooleanValueNode(['value' => $serialized]);
            }
            if (\is_int($serialized)) {
                return new \Builderius\GraphQL\Language\AST\IntValueNode(['value' => (string) $serialized]);
            }
            if (\is_float($serialized)) {
                // int cast with == used for performance reasons
                // phpcs:ignore
                if ((int) $serialized == $serialized) {
                    return new \Builderius\GraphQL\Language\AST\IntValueNode(['value' => (string) $serialized]);
                }
                return new \Builderius\GraphQL\Language\AST\FloatValueNode(['value' => (string) $serialized]);
            }
            if (\is_string($serialized)) {
                // Enum types use Enum literals.
                if ($type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                    return new \Builderius\GraphQL\Language\AST\EnumValueNode(['value' => $serialized]);
                }
                // ID types can use Int literals.
                $asInt = (int) $serialized;
                if ($type instanceof \Builderius\GraphQL\Type\Definition\IDType && (string) $asInt === $serialized) {
                    return new \Builderius\GraphQL\Language\AST\IntValueNode(['value' => $serialized]);
                }
                // Use json_encode, which uses the same string encoding as GraphQL,
                // then remove the quotes.
                return new \Builderius\GraphQL\Language\AST\StringValueNode(['value' => $serialized]);
            }
            throw new \Builderius\GraphQL\Error\InvariantViolation('Cannot convert value to AST: ' . \Builderius\GraphQL\Utils\Utils::printSafe($serialized));
        }
        throw new \Builderius\GraphQL\Error\Error('Unknown type: ' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . '.');
    }
    /**
     * Produces a PHP value given a GraphQL Value AST.
     *
     * A GraphQL type must be provided, which will be used to interpret different
     * GraphQL Value literals.
     *
     * Returns `null` when the value could not be validly coerced according to
     * the provided type.
     *
     * | GraphQL Value        | PHP Value     |
     * | -------------------- | ------------- |
     * | Input Object         | Assoc Array   |
     * | List                 | Array         |
     * | Boolean              | Boolean       |
     * | String               | String        |
     * | Int / Float          | Int / Float   |
     * | Enum Value           | Mixed         |
     * | Null Value           | null          |
     *
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null $valueNode
     * @param mixed[]|null                                                                                                                             $variables
     *
     * @return mixed[]|stdClass|null
     *
     * @throws Exception
     *
     * @api
     */
    public static function valueFromAST(?\Builderius\GraphQL\Language\AST\ValueNode $valueNode, \Builderius\GraphQL\Type\Definition\Type $type, ?array $variables = null)
    {
        $undefined = \Builderius\GraphQL\Utils\Utils::undefined();
        if ($valueNode === null) {
            // When there is no AST, then there is also no value.
            // Importantly, this is different from returning the GraphQL null value.
            return $undefined;
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            if ($valueNode instanceof \Builderius\GraphQL\Language\AST\NullValueNode) {
                // Invalid: intentionally return no value.
                return $undefined;
            }
            return self::valueFromAST($valueNode, $type->getWrappedType(), $variables);
        }
        if ($valueNode instanceof \Builderius\GraphQL\Language\AST\NullValueNode) {
            // This is explicitly returning the value null.
            return null;
        }
        if ($valueNode instanceof \Builderius\GraphQL\Language\AST\VariableNode) {
            $variableName = $valueNode->name->value;
            if (!$variables || !\array_key_exists($variableName, $variables)) {
                // No valid return value.
                return $undefined;
            }
            $variableValue = $variables[$variableName] ?? null;
            if ($variableValue === null && $type instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
                return $undefined;
                // Invalid: intentionally return no value.
            }
            // Note: This does no further checking that this variable is correct.
            // This assumes that this query has been validated and the variable
            // usage here is of the correct type.
            return $variables[$variableName];
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            $itemType = $type->getWrappedType();
            if ($valueNode instanceof \Builderius\GraphQL\Language\AST\ListValueNode) {
                $coercedValues = [];
                $itemNodes = $valueNode->values;
                foreach ($itemNodes as $itemNode) {
                    if (self::isMissingVariable($itemNode, $variables)) {
                        // If an array contains a missing variable, it is either coerced to
                        // null or if the item type is non-null, it considered invalid.
                        if ($itemType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
                            // Invalid: intentionally return no value.
                            return $undefined;
                        }
                        $coercedValues[] = null;
                    } else {
                        $itemValue = self::valueFromAST($itemNode, $itemType, $variables);
                        if ($undefined === $itemValue) {
                            // Invalid: intentionally return no value.
                            return $undefined;
                        }
                        $coercedValues[] = $itemValue;
                    }
                }
                return $coercedValues;
            }
            $coercedValue = self::valueFromAST($valueNode, $itemType, $variables);
            if ($undefined === $coercedValue) {
                // Invalid: intentionally return no value.
                return $undefined;
            }
            return [$coercedValue];
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
            if (!$valueNode instanceof \Builderius\GraphQL\Language\AST\ObjectValueNode) {
                // Invalid: intentionally return no value.
                return $undefined;
            }
            $coercedObj = [];
            $fields = $type->getFields();
            $fieldNodes = \Builderius\GraphQL\Utils\Utils::keyMap($valueNode->fields, static function ($field) {
                return $field->name->value;
            });
            foreach ($fields as $field) {
                $fieldName = $field->name;
                /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $fieldNode */
                $fieldNode = $fieldNodes[$fieldName] ?? null;
                if ($fieldNode === null || self::isMissingVariable($fieldNode->value, $variables)) {
                    if ($field->defaultValueExists()) {
                        $coercedObj[$fieldName] = $field->defaultValue;
                    } elseif ($field->getType() instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
                        // Invalid: intentionally return no value.
                        return $undefined;
                    }
                    continue;
                }
                $fieldValue = self::valueFromAST($fieldNode !== null ? $fieldNode->value : null, $field->getType(), $variables);
                if ($undefined === $fieldValue) {
                    // Invalid: intentionally return no value.
                    return $undefined;
                }
                $coercedObj[$fieldName] = $fieldValue;
            }
            return $coercedObj;
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
            if (!$valueNode instanceof \Builderius\GraphQL\Language\AST\EnumValueNode) {
                return $undefined;
            }
            $enumValue = $type->getValue($valueNode->value);
            if (!$enumValue) {
                return $undefined;
            }
            return $enumValue->value;
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ScalarType) {
            // Scalars fulfill parsing a literal value via parseLiteral().
            // Invalid values represent a failure to parse correctly, in which case
            // no value is returned.
            try {
                return $type->parseLiteral($valueNode, $variables);
            } catch (\Throwable $error) {
                return $undefined;
            }
        }
        throw new \Builderius\GraphQL\Error\Error('Unknown type: ' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . '.');
    }
    /**
     * Returns true if the provided valueNode is a variable which is not defined
     * in the set of variables.
     *
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $valueNode
     * @param mixed[]                                                                                                                             $variables
     *
     * @return bool
     */
    private static function isMissingVariable(\Builderius\GraphQL\Language\AST\ValueNode $valueNode, $variables)
    {
        return $valueNode instanceof \Builderius\GraphQL\Language\AST\VariableNode && (\count($variables) === 0 || !\array_key_exists($valueNode->name->value, $variables));
    }
    /**
     * Produces a PHP value given a GraphQL Value AST.
     *
     * Unlike `valueFromAST()`, no type is provided. The resulting PHP value
     * will reflect the provided GraphQL value AST.
     *
     * | GraphQL Value        | PHP Value     |
     * | -------------------- | ------------- |
     * | Input Object         | Assoc Array   |
     * | List                 | Array         |
     * | Boolean              | Boolean       |
     * | String               | String        |
     * | Int / Float          | Int / Float   |
     * | Enum                 | Mixed         |
     * | Null                 | null          |
     *
     * @param Node         $valueNode
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     *
     * @api
     */
    public static function valueFromASTUntyped($valueNode, ?array $variables = null)
    {
        switch (\true) {
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\NullValueNode:
                return null;
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\IntValueNode:
                return (int) $valueNode->value;
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\FloatValueNode:
                return (float) $valueNode->value;
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\StringValueNode:
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\EnumValueNode:
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\BooleanValueNode:
                return $valueNode->value;
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\ListValueNode:
                return \array_map(static function ($node) use($variables) {
                    return self::valueFromASTUntyped($node, $variables);
                }, \iterator_to_array($valueNode->values));
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\ObjectValueNode:
                return \array_combine(\array_map(static function ($field) : string {
                    return $field->name->value;
                }, \iterator_to_array($valueNode->fields)), \array_map(static function ($field) use($variables) {
                    return self::valueFromASTUntyped($field->value, $variables);
                }, \iterator_to_array($valueNode->fields)));
            case $valueNode instanceof \Builderius\GraphQL\Language\AST\VariableNode:
                $variableName = $valueNode->name->value;
                return $variables && isset($variables[$variableName]) ? $variables[$variableName] : null;
        }
        throw new \Builderius\GraphQL\Error\Error('Unexpected value kind: ' . $valueNode->kind . '.');
    }
    /**
     * Returns type definition for given AST Type node
     *
     * @param NamedTypeNode|ListTypeNode|NonNullTypeNode $inputTypeNode
     *
     * @return Type|null
     *
     * @throws Exception
     *
     * @api
     */
    public static function typeFromAST(\Builderius\GraphQL\Type\Schema $schema, $inputTypeNode)
    {
        if ($inputTypeNode instanceof \Builderius\GraphQL\Language\AST\ListTypeNode) {
            $innerType = self::typeFromAST($schema, $inputTypeNode->type);
            return $innerType ? new \Builderius\GraphQL\Type\Definition\ListOfType($innerType) : null;
        }
        if ($inputTypeNode instanceof \Builderius\GraphQL\Language\AST\NonNullTypeNode) {
            $innerType = self::typeFromAST($schema, $inputTypeNode->type);
            return $innerType ? new \Builderius\GraphQL\Type\Definition\NonNull($innerType) : null;
        }
        if ($inputTypeNode instanceof \Builderius\GraphQL\Language\AST\NamedTypeNode) {
            return $schema->getType($inputTypeNode->name->value);
        }
        throw new \Builderius\GraphQL\Error\Error('Unexpected type kind: ' . $inputTypeNode->kind . '.');
    }
    /**
     * Returns operation type ("query", "mutation" or "subscription") given a document and operation name
     *
     * @param string $operationName
     *
     * @return bool|string
     *
     * @api
     */
    public static function getOperation(\Builderius\GraphQL\Language\AST\DocumentNode $document, $operationName = null)
    {
        if ($document->definitions) {
            foreach ($document->definitions as $def) {
                if (!$def instanceof \Builderius\GraphQL\Language\AST\OperationDefinitionNode) {
                    continue;
                }
                if (!$operationName || isset($def->name->value) && $def->name->value === $operationName) {
                    return $def->operation;
                }
            }
        }
        return \false;
    }
}