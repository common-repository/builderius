<?php

declare (strict_types=1);
/**
 * Utility for finding breaking/dangerous changes between two schemas.
 */
namespace Builderius\GraphQL\Utils;

use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\EnumType;
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
use Builderius\GraphQL\Type\Schema;
use TypeError;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function class_alias;
use function sprintf;
class BreakingChangesFinder
{
    public const BREAKING_CHANGE_FIELD_CHANGED_KIND = 'FIELD_CHANGED_KIND';
    public const BREAKING_CHANGE_FIELD_REMOVED = 'FIELD_REMOVED';
    public const BREAKING_CHANGE_TYPE_CHANGED_KIND = 'TYPE_CHANGED_KIND';
    public const BREAKING_CHANGE_TYPE_REMOVED = 'TYPE_REMOVED';
    public const BREAKING_CHANGE_TYPE_REMOVED_FROM_UNION = 'TYPE_REMOVED_FROM_UNION';
    public const BREAKING_CHANGE_VALUE_REMOVED_FROM_ENUM = 'VALUE_REMOVED_FROM_ENUM';
    public const BREAKING_CHANGE_ARG_REMOVED = 'ARG_REMOVED';
    public const BREAKING_CHANGE_ARG_CHANGED_KIND = 'ARG_CHANGED_KIND';
    public const BREAKING_CHANGE_REQUIRED_ARG_ADDED = 'REQUIRED_ARG_ADDED';
    public const BREAKING_CHANGE_REQUIRED_INPUT_FIELD_ADDED = 'REQUIRED_INPUT_FIELD_ADDED';
    public const BREAKING_CHANGE_INTERFACE_REMOVED_FROM_OBJECT = 'INTERFACE_REMOVED_FROM_OBJECT';
    public const BREAKING_CHANGE_DIRECTIVE_REMOVED = 'DIRECTIVE_REMOVED';
    public const BREAKING_CHANGE_DIRECTIVE_ARG_REMOVED = 'DIRECTIVE_ARG_REMOVED';
    public const BREAKING_CHANGE_DIRECTIVE_LOCATION_REMOVED = 'DIRECTIVE_LOCATION_REMOVED';
    public const BREAKING_CHANGE_REQUIRED_DIRECTIVE_ARG_ADDED = 'REQUIRED_DIRECTIVE_ARG_ADDED';
    public const DANGEROUS_CHANGE_ARG_DEFAULT_VALUE_CHANGED = 'ARG_DEFAULT_VALUE_CHANGE';
    public const DANGEROUS_CHANGE_VALUE_ADDED_TO_ENUM = 'VALUE_ADDED_TO_ENUM';
    public const DANGEROUS_CHANGE_INTERFACE_ADDED_TO_OBJECT = 'INTERFACE_ADDED_TO_OBJECT';
    public const DANGEROUS_CHANGE_TYPE_ADDED_TO_UNION = 'TYPE_ADDED_TO_UNION';
    public const DANGEROUS_CHANGE_OPTIONAL_INPUT_FIELD_ADDED = 'OPTIONAL_INPUT_FIELD_ADDED';
    public const DANGEROUS_CHANGE_OPTIONAL_ARG_ADDED = 'OPTIONAL_ARG_ADDED';
    /**
     * Given two schemas, returns an Array containing descriptions of all the types
     * of breaking changes covered by the other functions down below.
     *
     * @return string[][]
     */
    public static function findBreakingChanges(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        return \array_merge(self::findRemovedTypes($oldSchema, $newSchema), self::findTypesThatChangedKind($oldSchema, $newSchema), self::findFieldsThatChangedTypeOnObjectOrInterfaceTypes($oldSchema, $newSchema), self::findFieldsThatChangedTypeOnInputObjectTypes($oldSchema, $newSchema)['breakingChanges'], self::findTypesRemovedFromUnions($oldSchema, $newSchema), self::findValuesRemovedFromEnums($oldSchema, $newSchema), self::findArgChanges($oldSchema, $newSchema)['breakingChanges'], self::findInterfacesRemovedFromObjectTypes($oldSchema, $newSchema), self::findRemovedDirectives($oldSchema, $newSchema), self::findRemovedDirectiveArgs($oldSchema, $newSchema), self::findAddedNonNullDirectiveArgs($oldSchema, $newSchema), self::findRemovedDirectiveLocations($oldSchema, $newSchema));
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to removing an entire type.
     *
     * @return string[][]
     */
    public static function findRemovedTypes(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $breakingChanges = [];
        foreach (\array_keys($oldTypeMap) as $typeName) {
            if (isset($newTypeMap[$typeName])) {
                continue;
            }
            $breakingChanges[] = ['type' => self::BREAKING_CHANGE_TYPE_REMOVED, 'description' => "{$typeName} was removed."];
        }
        return $breakingChanges;
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to changing the type of a type.
     *
     * @return string[][]
     */
    public static function findTypesThatChangedKind(\Builderius\GraphQL\Type\Schema $schemaA, \Builderius\GraphQL\Type\Schema $schemaB) : iterable
    {
        $schemaATypeMap = $schemaA->getTypeMap();
        $schemaBTypeMap = $schemaB->getTypeMap();
        $breakingChanges = [];
        foreach ($schemaATypeMap as $typeName => $schemaAType) {
            if (!isset($schemaBTypeMap[$typeName])) {
                continue;
            }
            $schemaBType = $schemaBTypeMap[$typeName];
            if ($schemaAType instanceof $schemaBType) {
                continue;
            }
            if ($schemaBType instanceof $schemaAType) {
                continue;
            }
            $schemaATypeKindName = self::typeKindName($schemaAType);
            $schemaBTypeKindName = self::typeKindName($schemaBType);
            $breakingChanges[] = ['type' => self::BREAKING_CHANGE_TYPE_CHANGED_KIND, 'description' => "{$typeName} changed from {$schemaATypeKindName} to {$schemaBTypeKindName}."];
        }
        return $breakingChanges;
    }
    /**
     * @return string
     *
     * @throws TypeError
     */
    private static function typeKindName(\Builderius\GraphQL\Type\Definition\Type $type)
    {
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ScalarType) {
            return 'a Scalar type';
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
            return 'an Object type';
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
            return 'an Interface type';
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
            return 'a Union type';
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
            return 'an Enum type';
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
            return 'an Input type';
        }
        throw new \TypeError('unknown type ' . $type->name);
    }
    /**
     * @return string[][]
     */
    public static function findFieldsThatChangedTypeOnObjectOrInterfaceTypes(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $breakingChanges = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!($oldType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $oldType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) || !($newType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $newType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) || !$newType instanceof $oldType) {
                continue;
            }
            $oldTypeFieldsDef = $oldType->getFields();
            $newTypeFieldsDef = $newType->getFields();
            foreach ($oldTypeFieldsDef as $fieldName => $fieldDefinition) {
                // Check if the field is missing on the type in the new schema.
                if (!isset($newTypeFieldsDef[$fieldName])) {
                    $breakingChanges[] = ['type' => self::BREAKING_CHANGE_FIELD_REMOVED, 'description' => "{$typeName}.{$fieldName} was removed."];
                } else {
                    $oldFieldType = $oldTypeFieldsDef[$fieldName]->getType();
                    $newFieldType = $newTypeFieldsDef[$fieldName]->getType();
                    $isSafe = self::isChangeSafeForObjectOrInterfaceField($oldFieldType, $newFieldType);
                    if (!$isSafe) {
                        $oldFieldTypeString = $oldFieldType instanceof \Builderius\GraphQL\Type\Definition\NamedType && $oldFieldType instanceof \Builderius\GraphQL\Type\Definition\Type ? $oldFieldType->name : $oldFieldType;
                        $newFieldTypeString = $newFieldType instanceof \Builderius\GraphQL\Type\Definition\NamedType && $newFieldType instanceof \Builderius\GraphQL\Type\Definition\Type ? $newFieldType->name : $newFieldType;
                        $breakingChanges[] = ['type' => self::BREAKING_CHANGE_FIELD_CHANGED_KIND, 'description' => "{$typeName}.{$fieldName} changed type from {$oldFieldTypeString} to {$newFieldTypeString}."];
                    }
                }
            }
        }
        return $breakingChanges;
    }
    /**
     * @return bool
     */
    private static function isChangeSafeForObjectOrInterfaceField(\Builderius\GraphQL\Type\Definition\Type $oldType, \Builderius\GraphQL\Type\Definition\Type $newType)
    {
        if ($oldType instanceof \Builderius\GraphQL\Type\Definition\NamedType) {
            return $newType instanceof \Builderius\GraphQL\Type\Definition\NamedType && $oldType->name === $newType->name || $newType instanceof \Builderius\GraphQL\Type\Definition\NonNull && self::isChangeSafeForObjectOrInterfaceField($oldType, $newType->getWrappedType());
        }
        if ($oldType instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            return $newType instanceof \Builderius\GraphQL\Type\Definition\ListOfType && self::isChangeSafeForObjectOrInterfaceField($oldType->getWrappedType(), $newType->getWrappedType()) || $newType instanceof \Builderius\GraphQL\Type\Definition\NonNull && self::isChangeSafeForObjectOrInterfaceField($oldType, $newType->getWrappedType());
        }
        if ($oldType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            // if they're both non-null, make sure the underlying types are compatible
            return $newType instanceof \Builderius\GraphQL\Type\Definition\NonNull && self::isChangeSafeForObjectOrInterfaceField($oldType->getWrappedType(), $newType->getWrappedType());
        }
        return \false;
    }
    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public static function findFieldsThatChangedTypeOnInputObjectTypes(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $breakingChanges = [];
        $dangerousChanges = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\InputObjectType || !$newType instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                continue;
            }
            $oldTypeFieldsDef = $oldType->getFields();
            $newTypeFieldsDef = $newType->getFields();
            foreach (\array_keys($oldTypeFieldsDef) as $fieldName) {
                if (!isset($newTypeFieldsDef[$fieldName])) {
                    $breakingChanges[] = ['type' => self::BREAKING_CHANGE_FIELD_REMOVED, 'description' => "{$typeName}.{$fieldName} was removed."];
                } else {
                    $oldFieldType = $oldTypeFieldsDef[$fieldName]->getType();
                    $newFieldType = $newTypeFieldsDef[$fieldName]->getType();
                    $isSafe = self::isChangeSafeForInputObjectFieldOrFieldArg($oldFieldType, $newFieldType);
                    if (!$isSafe) {
                        if ($oldFieldType instanceof \Builderius\GraphQL\Type\Definition\NamedType) {
                            $oldFieldTypeString = $oldFieldType->name;
                        } else {
                            $oldFieldTypeString = $oldFieldType;
                        }
                        if ($newFieldType instanceof \Builderius\GraphQL\Type\Definition\NamedType) {
                            $newFieldTypeString = $newFieldType->name;
                        } else {
                            $newFieldTypeString = $newFieldType;
                        }
                        $breakingChanges[] = ['type' => self::BREAKING_CHANGE_FIELD_CHANGED_KIND, 'description' => "{$typeName}.{$fieldName} changed type from {$oldFieldTypeString} to {$newFieldTypeString}."];
                    }
                }
            }
            // Check if a field was added to the input object type
            foreach ($newTypeFieldsDef as $fieldName => $fieldDef) {
                if (isset($oldTypeFieldsDef[$fieldName])) {
                    continue;
                }
                $newTypeName = $newType->name;
                if ($fieldDef->isRequired()) {
                    $breakingChanges[] = ['type' => self::BREAKING_CHANGE_REQUIRED_INPUT_FIELD_ADDED, 'description' => "A required field {$fieldName} on input type {$newTypeName} was added."];
                } else {
                    $dangerousChanges[] = ['type' => self::DANGEROUS_CHANGE_OPTIONAL_INPUT_FIELD_ADDED, 'description' => "An optional field {$fieldName} on input type {$newTypeName} was added."];
                }
            }
        }
        return ['breakingChanges' => $breakingChanges, 'dangerousChanges' => $dangerousChanges];
    }
    /**
     * @return bool
     */
    private static function isChangeSafeForInputObjectFieldOrFieldArg(\Builderius\GraphQL\Type\Definition\Type $oldType, \Builderius\GraphQL\Type\Definition\Type $newType)
    {
        if ($oldType instanceof \Builderius\GraphQL\Type\Definition\NamedType) {
            if (!$newType instanceof \Builderius\GraphQL\Type\Definition\NamedType) {
                return \false;
            }
            // if they're both named types, see if their names are equivalent
            return $oldType->name === $newType->name;
        }
        if ($oldType instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            // if they're both lists, make sure the underlying types are compatible
            return $newType instanceof \Builderius\GraphQL\Type\Definition\ListOfType && self::isChangeSafeForInputObjectFieldOrFieldArg($oldType->getWrappedType(), $newType->getWrappedType());
        }
        if ($oldType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            return $newType instanceof \Builderius\GraphQL\Type\Definition\NonNull && self::isChangeSafeForInputObjectFieldOrFieldArg($oldType->getWrappedType(), $newType->getWrappedType()) || !$newType instanceof \Builderius\GraphQL\Type\Definition\NonNull && self::isChangeSafeForInputObjectFieldOrFieldArg($oldType->getWrappedType(), $newType);
        }
        return \false;
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to removing types from a union type.
     *
     * @return string[][]
     */
    public static function findTypesRemovedFromUnions(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $typesRemovedFromUnion = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\UnionType || !$newType instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
                continue;
            }
            $typeNamesInNewUnion = [];
            foreach ($newType->getTypes() as $type) {
                $typeNamesInNewUnion[$type->name] = \true;
            }
            foreach ($oldType->getTypes() as $type) {
                if (isset($typeNamesInNewUnion[$type->name])) {
                    continue;
                }
                $typesRemovedFromUnion[] = ['type' => self::BREAKING_CHANGE_TYPE_REMOVED_FROM_UNION, 'description' => \sprintf('%s was removed from union type %s.', $type->name, $typeName)];
            }
        }
        return $typesRemovedFromUnion;
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to removing values from an enum type.
     *
     * @return string[][]
     */
    public static function findValuesRemovedFromEnums(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $valuesRemovedFromEnums = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\EnumType || !$newType instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                continue;
            }
            $valuesInNewEnum = [];
            foreach ($newType->getValues() as $value) {
                $valuesInNewEnum[$value->name] = \true;
            }
            foreach ($oldType->getValues() as $value) {
                if (isset($valuesInNewEnum[$value->name])) {
                    continue;
                }
                $valuesRemovedFromEnums[] = ['type' => self::BREAKING_CHANGE_VALUE_REMOVED_FROM_ENUM, 'description' => \sprintf('%s was removed from enum type %s.', $value->name, $typeName)];
            }
        }
        return $valuesRemovedFromEnums;
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any
     * breaking or dangerous changes in the newSchema related to arguments
     * (such as removal or change of type of an argument, or a change in an
     * argument's default value).
     *
     * @return array<string, array<int,array<string, string>>>
     */
    public static function findArgChanges(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $breakingChanges = [];
        $dangerousChanges = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!($oldType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $oldType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) || !($newType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $newType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) || !$newType instanceof $oldType) {
                continue;
            }
            $oldTypeFields = $oldType->getFields();
            $newTypeFields = $newType->getFields();
            foreach ($oldTypeFields as $fieldName => $oldField) {
                if (!isset($newTypeFields[$fieldName])) {
                    continue;
                }
                foreach ($oldField->args as $oldArgDef) {
                    $newArgs = $newTypeFields[$fieldName]->args;
                    $newArgDef = \Builderius\GraphQL\Utils\Utils::find($newArgs, static function ($arg) use($oldArgDef) : bool {
                        return $arg->name === $oldArgDef->name;
                    });
                    if ($newArgDef !== null) {
                        $isSafe = self::isChangeSafeForInputObjectFieldOrFieldArg($oldArgDef->getType(), $newArgDef->getType());
                        /** @var ScalarType|EnumType|InputObjectType|ListOfType|NonNull $oldArgType */
                        $oldArgType = $oldArgDef->getType();
                        $oldArgName = $oldArgDef->name;
                        if (!$isSafe) {
                            $newArgType = $newArgDef->getType();
                            $breakingChanges[] = ['type' => self::BREAKING_CHANGE_ARG_CHANGED_KIND, 'description' => "{$typeName}.{$fieldName} arg {$oldArgName} has changed type from {$oldArgType} to {$newArgType}"];
                        } elseif ($oldArgDef->defaultValueExists() && $oldArgDef->defaultValue !== $newArgDef->defaultValue) {
                            $dangerousChanges[] = ['type' => self::DANGEROUS_CHANGE_ARG_DEFAULT_VALUE_CHANGED, 'description' => "{$typeName}.{$fieldName} arg {$oldArgName} has changed defaultValue"];
                        }
                    } else {
                        $breakingChanges[] = ['type' => self::BREAKING_CHANGE_ARG_REMOVED, 'description' => \sprintf('%s.%s arg %s was removed', $typeName, $fieldName, $oldArgDef->name)];
                    }
                    // Check if arg was added to the field
                    foreach ($newTypeFields[$fieldName]->args as $newTypeFieldArgDef) {
                        $oldArgs = $oldTypeFields[$fieldName]->args;
                        $oldArgDef = \Builderius\GraphQL\Utils\Utils::find($oldArgs, static function ($arg) use($newTypeFieldArgDef) : bool {
                            return $arg->name === $newTypeFieldArgDef->name;
                        });
                        if ($oldArgDef !== null) {
                            continue;
                        }
                        $newTypeName = $newType->name;
                        $newArgName = $newTypeFieldArgDef->name;
                        if ($newTypeFieldArgDef->isRequired()) {
                            $breakingChanges[] = ['type' => self::BREAKING_CHANGE_REQUIRED_ARG_ADDED, 'description' => "A required arg {$newArgName} on {$newTypeName}.{$fieldName} was added"];
                        } else {
                            $dangerousChanges[] = ['type' => self::DANGEROUS_CHANGE_OPTIONAL_ARG_ADDED, 'description' => "An optional arg {$newArgName} on {$newTypeName}.{$fieldName} was added"];
                        }
                    }
                }
            }
        }
        return ['breakingChanges' => $breakingChanges, 'dangerousChanges' => $dangerousChanges];
    }
    /**
     * @return string[][]
     */
    public static function findInterfacesRemovedFromObjectTypes(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $breakingChanges = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || !$newType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                continue;
            }
            $oldInterfaces = $oldType->getInterfaces();
            $newInterfaces = $newType->getInterfaces();
            foreach ($oldInterfaces as $oldInterface) {
                $interface = \Builderius\GraphQL\Utils\Utils::find($newInterfaces, static function (\Builderius\GraphQL\Type\Definition\InterfaceType $interface) use($oldInterface) : bool {
                    return $interface->name === $oldInterface->name;
                });
                if ($interface !== null) {
                    continue;
                }
                $breakingChanges[] = ['type' => self::BREAKING_CHANGE_INTERFACE_REMOVED_FROM_OBJECT, 'description' => \sprintf('%s no longer implements interface %s.', $typeName, $oldInterface->name)];
            }
        }
        return $breakingChanges;
    }
    /**
     * @return string[][]
     */
    public static function findRemovedDirectives(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $removedDirectives = [];
        $newSchemaDirectiveMap = self::getDirectiveMapForSchema($newSchema);
        foreach ($oldSchema->getDirectives() as $directive) {
            if (isset($newSchemaDirectiveMap[$directive->name])) {
                continue;
            }
            $removedDirectives[] = ['type' => self::BREAKING_CHANGE_DIRECTIVE_REMOVED, 'description' => \sprintf('%s was removed', $directive->name)];
        }
        return $removedDirectives;
    }
    private static function getDirectiveMapForSchema(\Builderius\GraphQL\Type\Schema $schema)
    {
        return \Builderius\GraphQL\Utils\Utils::keyMap($schema->getDirectives(), static function ($dir) {
            return $dir->name;
        });
    }
    public static function findRemovedDirectiveArgs(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $removedDirectiveArgs = [];
        $oldSchemaDirectiveMap = self::getDirectiveMapForSchema($oldSchema);
        foreach ($newSchema->getDirectives() as $newDirective) {
            if (!isset($oldSchemaDirectiveMap[$newDirective->name])) {
                continue;
            }
            foreach (self::findRemovedArgsForDirectives($oldSchemaDirectiveMap[$newDirective->name], $newDirective) as $arg) {
                $removedDirectiveArgs[] = ['type' => self::BREAKING_CHANGE_DIRECTIVE_ARG_REMOVED, 'description' => \sprintf('%s was removed from %s', $arg->name, $newDirective->name)];
            }
        }
        return $removedDirectiveArgs;
    }
    public static function findRemovedArgsForDirectives(\Builderius\GraphQL\Type\Definition\Directive $oldDirective, \Builderius\GraphQL\Type\Definition\Directive $newDirective)
    {
        $removedArgs = [];
        $newArgMap = self::getArgumentMapForDirective($newDirective);
        foreach ($oldDirective->args as $arg) {
            if (isset($newArgMap[$arg->name])) {
                continue;
            }
            $removedArgs[] = $arg;
        }
        return $removedArgs;
    }
    private static function getArgumentMapForDirective(\Builderius\GraphQL\Type\Definition\Directive $directive)
    {
        return \Builderius\GraphQL\Utils\Utils::keyMap($directive->args ?? [], static function ($arg) {
            return $arg->name;
        });
    }
    public static function findAddedNonNullDirectiveArgs(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $addedNonNullableArgs = [];
        $oldSchemaDirectiveMap = self::getDirectiveMapForSchema($oldSchema);
        foreach ($newSchema->getDirectives() as $newDirective) {
            if (!isset($oldSchemaDirectiveMap[$newDirective->name])) {
                continue;
            }
            foreach (self::findAddedArgsForDirective($oldSchemaDirectiveMap[$newDirective->name], $newDirective) as $arg) {
                if (!$arg->isRequired()) {
                    continue;
                }
                $addedNonNullableArgs[] = ['type' => self::BREAKING_CHANGE_REQUIRED_DIRECTIVE_ARG_ADDED, 'description' => \sprintf('A required arg %s on directive %s was added', $arg->name, $newDirective->name)];
            }
        }
        return $addedNonNullableArgs;
    }
    /**
     * @return FieldArgument[]
     */
    public static function findAddedArgsForDirective(\Builderius\GraphQL\Type\Definition\Directive $oldDirective, \Builderius\GraphQL\Type\Definition\Directive $newDirective)
    {
        $addedArgs = [];
        $oldArgMap = self::getArgumentMapForDirective($oldDirective);
        foreach ($newDirective->args as $arg) {
            if (isset($oldArgMap[$arg->name])) {
                continue;
            }
            $addedArgs[] = $arg;
        }
        return $addedArgs;
    }
    /**
     * @return string[][]
     */
    public static function findRemovedDirectiveLocations(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $removedLocations = [];
        $oldSchemaDirectiveMap = self::getDirectiveMapForSchema($oldSchema);
        foreach ($newSchema->getDirectives() as $newDirective) {
            if (!isset($oldSchemaDirectiveMap[$newDirective->name])) {
                continue;
            }
            foreach (self::findRemovedLocationsForDirective($oldSchemaDirectiveMap[$newDirective->name], $newDirective) as $location) {
                $removedLocations[] = ['type' => self::BREAKING_CHANGE_DIRECTIVE_LOCATION_REMOVED, 'description' => \sprintf('%s was removed from %s', $location, $newDirective->name)];
            }
        }
        return $removedLocations;
    }
    public static function findRemovedLocationsForDirective(\Builderius\GraphQL\Type\Definition\Directive $oldDirective, \Builderius\GraphQL\Type\Definition\Directive $newDirective)
    {
        $removedLocations = [];
        $newLocationSet = \array_flip($newDirective->locations);
        foreach ($oldDirective->locations as $oldLocation) {
            if (\array_key_exists($oldLocation, $newLocationSet)) {
                continue;
            }
            $removedLocations[] = $oldLocation;
        }
        return $removedLocations;
    }
    /**
     * Given two schemas, returns an Array containing descriptions of all the types
     * of potentially dangerous changes covered by the other functions down below.
     *
     * @return string[][]
     */
    public static function findDangerousChanges(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        return \array_merge(self::findArgChanges($oldSchema, $newSchema)['dangerousChanges'], self::findValuesAddedToEnums($oldSchema, $newSchema), self::findInterfacesAddedToObjectTypes($oldSchema, $newSchema), self::findTypesAddedToUnions($oldSchema, $newSchema), self::findFieldsThatChangedTypeOnInputObjectTypes($oldSchema, $newSchema)['dangerousChanges']);
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any dangerous
     * changes in the newSchema related to adding values to an enum type.
     *
     * @return string[][]
     */
    public static function findValuesAddedToEnums(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $valuesAddedToEnums = [];
        foreach ($oldTypeMap as $typeName => $oldType) {
            $newType = $newTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\EnumType || !$newType instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                continue;
            }
            $valuesInOldEnum = [];
            foreach ($oldType->getValues() as $value) {
                $valuesInOldEnum[$value->name] = \true;
            }
            foreach ($newType->getValues() as $value) {
                if (isset($valuesInOldEnum[$value->name])) {
                    continue;
                }
                $valuesAddedToEnums[] = ['type' => self::DANGEROUS_CHANGE_VALUE_ADDED_TO_ENUM, 'description' => \sprintf('%s was added to enum type %s.', $value->name, $typeName)];
            }
        }
        return $valuesAddedToEnums;
    }
    /**
     * @return string[][]
     */
    public static function findInterfacesAddedToObjectTypes(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $interfacesAddedToObjectTypes = [];
        foreach ($newTypeMap as $typeName => $newType) {
            $oldType = $oldTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || !$newType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                continue;
            }
            $oldInterfaces = $oldType->getInterfaces();
            $newInterfaces = $newType->getInterfaces();
            foreach ($newInterfaces as $newInterface) {
                $interface = \Builderius\GraphQL\Utils\Utils::find($oldInterfaces, static function (\Builderius\GraphQL\Type\Definition\InterfaceType $interface) use($newInterface) : bool {
                    return $interface->name === $newInterface->name;
                });
                if ($interface !== null) {
                    continue;
                }
                $interfacesAddedToObjectTypes[] = ['type' => self::DANGEROUS_CHANGE_INTERFACE_ADDED_TO_OBJECT, 'description' => \sprintf('%s added to interfaces implemented by %s.', $newInterface->name, $typeName)];
            }
        }
        return $interfacesAddedToObjectTypes;
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any dangerous
     * changes in the newSchema related to adding types to a union type.
     *
     * @return string[][]
     */
    public static function findTypesAddedToUnions(\Builderius\GraphQL\Type\Schema $oldSchema, \Builderius\GraphQL\Type\Schema $newSchema)
    {
        $oldTypeMap = $oldSchema->getTypeMap();
        $newTypeMap = $newSchema->getTypeMap();
        $typesAddedToUnion = [];
        foreach ($newTypeMap as $typeName => $newType) {
            $oldType = $oldTypeMap[$typeName] ?? null;
            if (!$oldType instanceof \Builderius\GraphQL\Type\Definition\UnionType || !$newType instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
                continue;
            }
            $typeNamesInOldUnion = [];
            foreach ($oldType->getTypes() as $type) {
                $typeNamesInOldUnion[$type->name] = \true;
            }
            foreach ($newType->getTypes() as $type) {
                if (isset($typeNamesInOldUnion[$type->name])) {
                    continue;
                }
                $typesAddedToUnion[] = ['type' => self::DANGEROUS_CHANGE_TYPE_ADDED_TO_UNION, 'description' => \sprintf('%s was added to union type %s.', $type->name, $typeName)];
            }
        }
        return $typesAddedToUnion;
    }
}
\class_alias(\Builderius\GraphQL\Utils\BreakingChangesFinder::class, 'Builderius\\GraphQL\\Utils\\FindBreakingChanges');
