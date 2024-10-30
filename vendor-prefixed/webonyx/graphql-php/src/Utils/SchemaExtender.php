<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Utils;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\EnumTypeExtensionNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\SchemaDefinitionNode;
use Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode;
use Builderius\GraphQL\Language\AST\TypeDefinitionNode;
use Builderius\GraphQL\Language\AST\TypeExtensionNode;
use Builderius\GraphQL\Language\AST\UnionTypeExtensionNode;
use Builderius\GraphQL\Type\Definition\CustomScalarType;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\EnumValueDefinition;
use Builderius\GraphQL\Type\Definition\FieldArgument;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NamedType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Definition\UnionType;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Validator\DocumentValidator;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function count;
class SchemaExtender
{
    const SCHEMA_EXTENSION = 'SchemaExtension';
    /** @var Type[] */
    protected static $extendTypeCache;
    /** @var mixed[] */
    protected static $typeExtensionsMap;
    /** @var ASTDefinitionBuilder */
    protected static $astBuilder;
    /**
     * @return TypeExtensionNode[]|null
     */
    protected static function getExtensionASTNodes(\Builderius\GraphQL\Type\Definition\NamedType $type) : ?array
    {
        if (!$type instanceof \Builderius\GraphQL\Type\Definition\Type) {
            return null;
        }
        $name = $type->name;
        if ($type->extensionASTNodes !== null) {
            if (isset(static::$typeExtensionsMap[$name])) {
                return \array_merge($type->extensionASTNodes, static::$typeExtensionsMap[$name]);
            }
            return $type->extensionASTNodes;
        }
        return static::$typeExtensionsMap[$name] ?? null;
    }
    /**
     * @throws Error
     */
    protected static function checkExtensionNode(\Builderius\GraphQL\Type\Definition\Type $type, \Builderius\GraphQL\Language\AST\Node $node) : void
    {
        switch (\true) {
            case $node instanceof \Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode:
                if (!$type instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                    throw new \Builderius\GraphQL\Error\Error('Cannot extend non-object type "' . $type->name . '".', [$node]);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode:
                if (!$type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
                    throw new \Builderius\GraphQL\Error\Error('Cannot extend non-interface type "' . $type->name . '".', [$node]);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\EnumTypeExtensionNode:
                if (!$type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                    throw new \Builderius\GraphQL\Error\Error('Cannot extend non-enum type "' . $type->name . '".', [$node]);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\UnionTypeExtensionNode:
                if (!$type instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
                    throw new \Builderius\GraphQL\Error\Error('Cannot extend non-union type "' . $type->name . '".', [$node]);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode:
                if (!$type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                    throw new \Builderius\GraphQL\Error\Error('Cannot extend non-input object type "' . $type->name . '".', [$node]);
                }
                break;
        }
    }
    protected static function extendScalarType(\Builderius\GraphQL\Type\Definition\ScalarType $type) : \Builderius\GraphQL\Type\Definition\CustomScalarType
    {
        return new \Builderius\GraphQL\Type\Definition\CustomScalarType(['name' => $type->name, 'description' => $type->description, 'astNode' => $type->astNode, 'serialize' => $type->config['serialize'] ?? null, 'parseValue' => $type->config['parseValue'] ?? null, 'parseLiteral' => $type->config['parseLiteral'] ?? null, 'extensionASTNodes' => static::getExtensionASTNodes($type)]);
    }
    protected static function extendUnionType(\Builderius\GraphQL\Type\Definition\UnionType $type) : \Builderius\GraphQL\Type\Definition\UnionType
    {
        return new \Builderius\GraphQL\Type\Definition\UnionType(['name' => $type->name, 'description' => $type->description, 'types' => static function () use($type) : array {
            return static::extendPossibleTypes($type);
        }, 'astNode' => $type->astNode, 'resolveType' => $type->config['resolveType'] ?? null, 'extensionASTNodes' => static::getExtensionASTNodes($type)]);
    }
    protected static function extendEnumType(\Builderius\GraphQL\Type\Definition\EnumType $type) : \Builderius\GraphQL\Type\Definition\EnumType
    {
        return new \Builderius\GraphQL\Type\Definition\EnumType(['name' => $type->name, 'description' => $type->description, 'values' => static::extendValueMap($type), 'astNode' => $type->astNode, 'extensionASTNodes' => static::getExtensionASTNodes($type)]);
    }
    protected static function extendInputObjectType(\Builderius\GraphQL\Type\Definition\InputObjectType $type) : \Builderius\GraphQL\Type\Definition\InputObjectType
    {
        return new \Builderius\GraphQL\Type\Definition\InputObjectType(['name' => $type->name, 'description' => $type->description, 'fields' => static function () use($type) : array {
            return static::extendInputFieldMap($type);
        }, 'astNode' => $type->astNode, 'extensionASTNodes' => static::getExtensionASTNodes($type)]);
    }
    /**
     * @return mixed[]
     */
    protected static function extendInputFieldMap(\Builderius\GraphQL\Type\Definition\InputObjectType $type) : array
    {
        $newFieldMap = [];
        $oldFieldMap = $type->getFields();
        foreach ($oldFieldMap as $fieldName => $field) {
            $newFieldMap[$fieldName] = ['description' => $field->description, 'type' => static::extendType($field->getType()), 'astNode' => $field->astNode];
            if (!$field->defaultValueExists()) {
                continue;
            }
            $newFieldMap[$fieldName]['defaultValue'] = $field->defaultValue;
        }
        $extensions = static::$typeExtensionsMap[$type->name] ?? null;
        if ($extensions !== null) {
            foreach ($extensions as $extension) {
                foreach ($extension->fields as $field) {
                    $fieldName = $field->name->value;
                    if (isset($oldFieldMap[$fieldName])) {
                        throw new \Builderius\GraphQL\Error\Error('Field "' . $type->name . '.' . $fieldName . '" already exists in the schema. It cannot also be defined in this type extension.', [$field]);
                    }
                    $newFieldMap[$fieldName] = static::$astBuilder->buildInputField($field);
                }
            }
        }
        return $newFieldMap;
    }
    /**
     * @return mixed[]
     */
    protected static function extendValueMap(\Builderius\GraphQL\Type\Definition\EnumType $type) : array
    {
        $newValueMap = [];
        /** @var EnumValueDefinition[] $oldValueMap */
        $oldValueMap = [];
        foreach ($type->getValues() as $value) {
            $oldValueMap[$value->name] = $value;
        }
        foreach ($oldValueMap as $key => $value) {
            $newValueMap[$key] = ['name' => $value->name, 'description' => $value->description, 'value' => $value->value, 'deprecationReason' => $value->deprecationReason, 'astNode' => $value->astNode];
        }
        $extensions = static::$typeExtensionsMap[$type->name] ?? null;
        if ($extensions !== null) {
            foreach ($extensions as $extension) {
                foreach ($extension->values as $value) {
                    $valueName = $value->name->value;
                    if (isset($oldValueMap[$valueName])) {
                        throw new \Builderius\GraphQL\Error\Error('Enum value "' . $type->name . '.' . $valueName . '" already exists in the schema. It cannot also be defined in this type extension.', [$value]);
                    }
                    $newValueMap[$valueName] = static::$astBuilder->buildEnumValue($value);
                }
            }
        }
        return $newValueMap;
    }
    /**
     * @return ObjectType[]
     */
    protected static function extendPossibleTypes(\Builderius\GraphQL\Type\Definition\UnionType $type) : array
    {
        $possibleTypes = \array_map(static function ($type) {
            return static::extendNamedType($type);
        }, $type->getTypes());
        $extensions = static::$typeExtensionsMap[$type->name] ?? null;
        if ($extensions !== null) {
            foreach ($extensions as $extension) {
                foreach ($extension->types as $namedType) {
                    $possibleTypes[] = static::$astBuilder->buildType($namedType);
                }
            }
        }
        return $possibleTypes;
    }
    /**
     * @return InterfaceType[]
     */
    protected static function extendImplementedInterfaces(\Builderius\GraphQL\Type\Definition\ObjectType $type) : array
    {
        $interfaces = \array_map(static function (\Builderius\GraphQL\Type\Definition\InterfaceType $interfaceType) {
            return static::extendNamedType($interfaceType);
        }, $type->getInterfaces());
        $extensions = static::$typeExtensionsMap[$type->name] ?? null;
        if ($extensions !== null) {
            /** @var ObjectTypeExtensionNode $extension */
            foreach ($extensions as $extension) {
                foreach ($extension->interfaces as $namedType) {
                    $interfaces[] = static::$astBuilder->buildType($namedType);
                }
            }
        }
        return $interfaces;
    }
    protected static function extendType($typeDef)
    {
        if ($typeDef instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            return \Builderius\GraphQL\Type\Definition\Type::listOf(static::extendType($typeDef->getOfType()));
        }
        if ($typeDef instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            return \Builderius\GraphQL\Type\Definition\Type::nonNull(static::extendType($typeDef->getWrappedType()));
        }
        return static::extendNamedType($typeDef);
    }
    /**
     * @param FieldArgument[] $args
     *
     * @return mixed[]
     */
    protected static function extendArgs(array $args) : array
    {
        return \Builderius\GraphQL\Utils\Utils::keyValMap($args, static function (\Builderius\GraphQL\Type\Definition\FieldArgument $arg) : string {
            return $arg->name;
        }, static function (\Builderius\GraphQL\Type\Definition\FieldArgument $arg) : array {
            $def = ['type' => static::extendType($arg->getType()), 'description' => $arg->description, 'astNode' => $arg->astNode];
            if ($arg->defaultValueExists()) {
                $def['defaultValue'] = $arg->defaultValue;
            }
            return $def;
        });
    }
    /**
     * @param InterfaceType|ObjectType $type
     *
     * @return mixed[]
     *
     * @throws Error
     */
    protected static function extendFieldMap($type) : array
    {
        $newFieldMap = [];
        $oldFieldMap = $type->getFields();
        foreach (\array_keys($oldFieldMap) as $fieldName) {
            $field = $oldFieldMap[$fieldName];
            $newFieldMap[$fieldName] = ['name' => $fieldName, 'description' => $field->description, 'deprecationReason' => $field->deprecationReason, 'type' => static::extendType($field->getType()), 'args' => static::extendArgs($field->args), 'astNode' => $field->astNode, 'resolve' => $field->resolveFn];
        }
        $extensions = static::$typeExtensionsMap[$type->name] ?? null;
        if ($extensions !== null) {
            foreach ($extensions as $extension) {
                foreach ($extension->fields as $field) {
                    $fieldName = $field->name->value;
                    if (isset($oldFieldMap[$fieldName])) {
                        throw new \Builderius\GraphQL\Error\Error('Field "' . $type->name . '.' . $fieldName . '" already exists in the schema. It cannot also be defined in this type extension.', [$field]);
                    }
                    $newFieldMap[$fieldName] = static::$astBuilder->buildField($field);
                }
            }
        }
        return $newFieldMap;
    }
    protected static function extendObjectType(\Builderius\GraphQL\Type\Definition\ObjectType $type) : \Builderius\GraphQL\Type\Definition\ObjectType
    {
        return new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => $type->name, 'description' => $type->description, 'interfaces' => static function () use($type) : array {
            return static::extendImplementedInterfaces($type);
        }, 'fields' => static function () use($type) : array {
            return static::extendFieldMap($type);
        }, 'astNode' => $type->astNode, 'extensionASTNodes' => static::getExtensionASTNodes($type), 'isTypeOf' => $type->config['isTypeOf'] ?? null, 'resolveField' => $type->resolveFieldFn ?? null]);
    }
    protected static function extendInterfaceType(\Builderius\GraphQL\Type\Definition\InterfaceType $type) : \Builderius\GraphQL\Type\Definition\InterfaceType
    {
        return new \Builderius\GraphQL\Type\Definition\InterfaceType(['name' => $type->name, 'description' => $type->description, 'fields' => static function () use($type) : array {
            return static::extendFieldMap($type);
        }, 'astNode' => $type->astNode, 'extensionASTNodes' => static::getExtensionASTNodes($type), 'resolveType' => $type->config['resolveType'] ?? null]);
    }
    protected static function isSpecifiedScalarType(\Builderius\GraphQL\Type\Definition\Type $type) : bool
    {
        return $type instanceof \Builderius\GraphQL\Type\Definition\NamedType && ($type->name === \Builderius\GraphQL\Type\Definition\Type::STRING || $type->name === \Builderius\GraphQL\Type\Definition\Type::INT || $type->name === \Builderius\GraphQL\Type\Definition\Type::FLOAT || $type->name === \Builderius\GraphQL\Type\Definition\Type::BOOLEAN || $type->name === \Builderius\GraphQL\Type\Definition\Type::ID);
    }
    protected static function extendNamedType(\Builderius\GraphQL\Type\Definition\Type $type)
    {
        if (\Builderius\GraphQL\Type\Introspection::isIntrospectionType($type) || static::isSpecifiedScalarType($type)) {
            return $type;
        }
        $name = $type->name;
        if (!isset(static::$extendTypeCache[$name])) {
            if ($type instanceof \Builderius\GraphQL\Type\Definition\ScalarType) {
                static::$extendTypeCache[$name] = static::extendScalarType($type);
            } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                static::$extendTypeCache[$name] = static::extendObjectType($type);
            } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
                static::$extendTypeCache[$name] = static::extendInterfaceType($type);
            } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
                static::$extendTypeCache[$name] = static::extendUnionType($type);
            } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                static::$extendTypeCache[$name] = static::extendEnumType($type);
            } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                static::$extendTypeCache[$name] = static::extendInputObjectType($type);
            }
        }
        return static::$extendTypeCache[$name];
    }
    /**
     * @return mixed|null
     */
    protected static function extendMaybeNamedType(?\Builderius\GraphQL\Type\Definition\NamedType $type = null)
    {
        if ($type !== null) {
            return static::extendNamedType($type);
        }
        return null;
    }
    /**
     * @param DirectiveDefinitionNode[] $directiveDefinitions
     *
     * @return Directive[]
     */
    protected static function getMergedDirectives(\Builderius\GraphQL\Type\Schema $schema, array $directiveDefinitions) : array
    {
        $existingDirectives = \array_map(static function (\Builderius\GraphQL\Type\Definition\Directive $directive) : Directive {
            return static::extendDirective($directive);
        }, $schema->getDirectives());
        \Builderius\GraphQL\Utils\Utils::invariant(\count($existingDirectives) > 0, 'schema must have default directives');
        return \array_merge($existingDirectives, \array_map(static function (\Builderius\GraphQL\Language\AST\DirectiveDefinitionNode $directive) {
            return static::$astBuilder->buildDirective($directive);
        }, $directiveDefinitions));
    }
    protected static function extendDirective(\Builderius\GraphQL\Type\Definition\Directive $directive) : \Builderius\GraphQL\Type\Definition\Directive
    {
        return new \Builderius\GraphQL\Type\Definition\Directive(['name' => $directive->name, 'description' => $directive->description, 'locations' => $directive->locations, 'args' => static::extendArgs($directive->args), 'astNode' => $directive->astNode]);
    }
    /**
     * @param mixed[]|null $options
     */
    public static function extend(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $documentAST, ?array $options = null) : \Builderius\GraphQL\Type\Schema
    {
        if ($options === null || !(isset($options['assumeValid']) || isset($options['assumeValidSDL']))) {
            \Builderius\GraphQL\Validator\DocumentValidator::assertValidSDLExtension($documentAST, $schema);
        }
        $typeDefinitionMap = [];
        static::$typeExtensionsMap = [];
        $directiveDefinitions = [];
        /** @var SchemaDefinitionNode|null $schemaDef */
        $schemaDef = null;
        /** @var SchemaTypeExtensionNode[] $schemaExtensions */
        $schemaExtensions = [];
        $definitionsCount = \count($documentAST->definitions);
        for ($i = 0; $i < $definitionsCount; $i++) {
            /** @var Node $def */
            $def = $documentAST->definitions[$i];
            if ($def instanceof \Builderius\GraphQL\Language\AST\SchemaDefinitionNode) {
                $schemaDef = $def;
            } elseif ($def instanceof \Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode) {
                $schemaExtensions[] = $def;
            } elseif ($def instanceof \Builderius\GraphQL\Language\AST\TypeDefinitionNode) {
                $typeName = isset($def->name) ? $def->name->value : null;
                try {
                    $type = $schema->getType($typeName);
                } catch (\Builderius\GraphQL\Error\Error $error) {
                    $type = null;
                }
                if ($type) {
                    throw new \Builderius\GraphQL\Error\Error('Type "' . $typeName . '" already exists in the schema. It cannot also be defined in this type definition.', [$def]);
                }
                $typeDefinitionMap[$typeName] = $def;
            } elseif ($def instanceof \Builderius\GraphQL\Language\AST\TypeExtensionNode) {
                $extendedTypeName = isset($def->name) ? $def->name->value : null;
                $existingType = $schema->getType($extendedTypeName);
                if ($existingType === null) {
                    throw new \Builderius\GraphQL\Error\Error('Cannot extend type "' . $extendedTypeName . '" because it does not exist in the existing schema.', [$def]);
                }
                static::checkExtensionNode($existingType, $def);
                $existingTypeExtensions = static::$typeExtensionsMap[$extendedTypeName] ?? null;
                static::$typeExtensionsMap[$extendedTypeName] = $existingTypeExtensions !== null ? \array_merge($existingTypeExtensions, [$def]) : [$def];
            } elseif ($def instanceof \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode) {
                $directiveName = $def->name->value;
                $existingDirective = $schema->getDirective($directiveName);
                if ($existingDirective !== null) {
                    throw new \Builderius\GraphQL\Error\Error('Directive "' . $directiveName . '" already exists in the schema. It cannot be redefined.', [$def]);
                }
                $directiveDefinitions[] = $def;
            }
        }
        if (\count(static::$typeExtensionsMap) === 0 && \count($typeDefinitionMap) === 0 && \count($directiveDefinitions) === 0 && \count($schemaExtensions) === 0 && $schemaDef === null) {
            return $schema;
        }
        static::$astBuilder = new \Builderius\GraphQL\Utils\ASTDefinitionBuilder($typeDefinitionMap, $options, static function (string $typeName) use($schema) {
            /** @var ScalarType|ObjectType|InterfaceType|UnionType|EnumType|InputObjectType $existingType */
            $existingType = $schema->getType($typeName);
            if ($existingType !== null) {
                return static::extendNamedType($existingType);
            }
            throw new \Builderius\GraphQL\Error\Error('Unknown type: "' . $typeName . '". Ensure that this type exists either in the original schema, or is added in a type definition.', [$typeName]);
        });
        static::$extendTypeCache = [];
        $operationTypes = ['query' => static::extendMaybeNamedType($schema->getQueryType()), 'mutation' => static::extendMaybeNamedType($schema->getMutationType()), 'subscription' => static::extendMaybeNamedType($schema->getSubscriptionType())];
        if ($schemaDef) {
            foreach ($schemaDef->operationTypes as $operationType) {
                $operation = $operationType->operation;
                $type = $operationType->type;
                if (isset($operationTypes[$operation])) {
                    throw new \Builderius\GraphQL\Error\Error('Must provide only one ' . $operation . ' type in schema.');
                }
                $operationTypes[$operation] = static::$astBuilder->buildType($type);
            }
        }
        foreach ($schemaExtensions as $schemaExtension) {
            if ($schemaExtension->operationTypes === null) {
                continue;
            }
            foreach ($schemaExtension->operationTypes as $operationType) {
                $operation = $operationType->operation;
                if (isset($operationTypes[$operation])) {
                    throw new \Builderius\GraphQL\Error\Error('Must provide only one ' . $operation . ' type in schema.');
                }
                $operationTypes[$operation] = static::$astBuilder->buildType($operationType->type);
            }
        }
        $schemaExtensionASTNodes = \array_merge($schema->extensionASTNodes, $schemaExtensions);
        $types = \array_merge(
            // Iterate through all types, getting the type definition for each, ensuring
            // that any type not directly referenced by a field will get created.
            \array_map(static function ($type) {
                return static::extendNamedType($type);
            }, \array_values($schema->getTypeMap())),
            // Do the same with new types.
            \array_map(static function ($type) : Type {
                return static::$astBuilder->buildType($type);
            }, \array_values($typeDefinitionMap))
        );
        return new \Builderius\GraphQL\Type\Schema(['query' => $operationTypes['query'], 'mutation' => $operationTypes['mutation'], 'subscription' => $operationTypes['subscription'], 'types' => $types, 'directives' => static::getMergedDirectives($schema, $directiveDefinitions), 'astNode' => $schema->getAstNode(), 'extensionASTNodes' => $schemaExtensionASTNodes]);
    }
}
