<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Utils;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\ListTypeNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\NamedTypeNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NonNullTypeNode;
use Builderius\GraphQL\Language\AST\ObjectFieldNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Type\Definition\CompositeType;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\FieldArgument;
use Builderius\GraphQL\Type\Definition\FieldDefinition;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\OutputType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Definition\UnionType;
use Builderius\GraphQL\Type\Definition\WrappingType;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Type\Schema;
use function array_map;
use function array_merge;
use function array_pop;
use function count;
use function is_array;
use function sprintf;
class TypeInfo
{
    /** @var Schema */
    private $schema;
    /** @var array<(OutputType&Type)|null> */
    private $typeStack;
    /** @var array<(CompositeType&Type)|null> */
    private $parentTypeStack;
    /** @var array<(InputType&Type)|null> */
    private $inputTypeStack;
    /** @var array<FieldDefinition> */
    private $fieldDefStack;
    /** @var array<mixed> */
    private $defaultValueStack;
    /** @var Directive|null */
    private $directive;
    /** @var FieldArgument|null */
    private $argument;
    /** @var mixed */
    private $enumValue;
    /**
     * @param Type|null $initialType
     */
    public function __construct(\Builderius\GraphQL\Type\Schema $schema, $initialType = null)
    {
        $this->schema = $schema;
        $this->typeStack = [];
        $this->parentTypeStack = [];
        $this->inputTypeStack = [];
        $this->fieldDefStack = [];
        $this->defaultValueStack = [];
        if ($initialType === null) {
            return;
        }
        if (\Builderius\GraphQL\Type\Definition\Type::isInputType($initialType)) {
            $this->inputTypeStack[] = $initialType;
        }
        if (\Builderius\GraphQL\Type\Definition\Type::isCompositeType($initialType)) {
            $this->parentTypeStack[] = $initialType;
        }
        if (!\Builderius\GraphQL\Type\Definition\Type::isOutputType($initialType)) {
            return;
        }
        $this->typeStack[] = $initialType;
    }
    /**
     * @deprecated moved to GraphQL\Utils\TypeComparators
     *
     * @codeCoverageIgnore
     */
    public static function isEqualType(\Builderius\GraphQL\Type\Definition\Type $typeA, \Builderius\GraphQL\Type\Definition\Type $typeB) : bool
    {
        return \Builderius\GraphQL\Utils\TypeComparators::isEqualType($typeA, $typeB);
    }
    /**
     * @deprecated moved to GraphQL\Utils\TypeComparators
     *
     * @codeCoverageIgnore
     */
    public static function isTypeSubTypeOf(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\Type $maybeSubType, \Builderius\GraphQL\Type\Definition\Type $superType)
    {
        return \Builderius\GraphQL\Utils\TypeComparators::isTypeSubTypeOf($schema, $maybeSubType, $superType);
    }
    /**
     * @deprecated moved to GraphQL\Utils\TypeComparators
     *
     * @codeCoverageIgnore
     */
    public static function doTypesOverlap(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\CompositeType $typeA, \Builderius\GraphQL\Type\Definition\CompositeType $typeB)
    {
        return \Builderius\GraphQL\Utils\TypeComparators::doTypesOverlap($schema, $typeA, $typeB);
    }
    /**
     * Given root type scans through all fields to find nested types. Returns array where keys are for type name
     * and value contains corresponding type instance.
     *
     * Example output:
     * [
     *     'String' => $instanceOfStringType,
     *     'MyType' => $instanceOfMyType,
     *     ...
     * ]
     *
     * @param Type|null   $type
     * @param Type[]|null $typeMap
     *
     * @return Type[]|null
     */
    public static function extractTypes($type, ?array $typeMap = null)
    {
        if (!$typeMap) {
            $typeMap = [];
        }
        if (!$type) {
            return $typeMap;
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\WrappingType) {
            return self::extractTypes($type->getWrappedType(\true), $typeMap);
        }
        if (!$type instanceof \Builderius\GraphQL\Type\Definition\Type) {
            // Preserve these invalid types in map (at numeric index) to make them
            // detectable during $schema->validate()
            $i = 0;
            $alreadyInMap = \false;
            while (isset($typeMap[$i])) {
                $alreadyInMap = $alreadyInMap || $typeMap[$i] === $type;
                $i++;
            }
            if (!$alreadyInMap) {
                $typeMap[$i] = $type;
            }
            return $typeMap;
        }
        if (isset($typeMap[$type->name])) {
            /*Utils::invariant(
                  $typeMap[$type->name] === $type,
                  sprintf('Schema must contain unique named types but contains multiple types named "%s" ', $type) .
                  '(see http://webonyx.github.io/graphql-php/type-system/#type-registry).'
              );*/
            return $typeMap;
        }
        $typeMap[$type->name] = $type;
        $nestedTypes = [];
        if ($type instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
            $nestedTypes = $type->getTypes();
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
            $nestedTypes = \array_merge($nestedTypes, $type->getInterfaces());
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
            foreach ($type->getFields() as $fieldName => $field) {
                if (\count($field->args ?? []) > 0) {
                    $fieldArgTypes = \array_map(static function (\Builderius\GraphQL\Type\Definition\FieldArgument $arg) : Type {
                        return $arg->getType();
                    }, $field->args);
                    $nestedTypes = \array_merge($nestedTypes, $fieldArgTypes);
                }
                $nestedTypes[] = $field->getType();
            }
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
            foreach ($type->getFields() as $fieldName => $field) {
                $nestedTypes[] = $field->getType();
            }
        }
        foreach ($nestedTypes as $nestedType) {
            $typeMap = self::extractTypes($nestedType, $typeMap);
        }
        return $typeMap;
    }
    /**
     * @param Type[] $typeMap
     *
     * @return Type[]
     */
    public static function extractTypesFromDirectives(\Builderius\GraphQL\Type\Definition\Directive $directive, array $typeMap = [])
    {
        if (\is_array($directive->args)) {
            foreach ($directive->args as $arg) {
                $typeMap = self::extractTypes($arg->getType(), $typeMap);
            }
        }
        return $typeMap;
    }
    /**
     * @return (Type&InputType)|null
     */
    public function getParentInputType() : ?\Builderius\GraphQL\Type\Definition\InputType
    {
        return $this->inputTypeStack[\count($this->inputTypeStack) - 2] ?? null;
    }
    public function getArgument() : ?\Builderius\GraphQL\Type\Definition\FieldArgument
    {
        return $this->argument;
    }
    /**
     * @return mixed
     */
    public function getEnumValue()
    {
        return $this->enumValue;
    }
    public function enter(\Builderius\GraphQL\Language\AST\Node $node)
    {
        $schema = $this->schema;
        // Note: many of the types below are explicitly typed as "mixed" to drop
        // any assumptions of a valid schema to ensure runtime types are properly
        // checked before continuing since TypeInfo is used as part of validation
        // which occurs before guarantees of schema and document validity.
        switch (\true) {
            case $node instanceof \Builderius\GraphQL\Language\AST\SelectionSetNode:
                $namedType = \Builderius\GraphQL\Type\Definition\Type::getNamedType($this->getType());
                $this->parentTypeStack[] = \Builderius\GraphQL\Type\Definition\Type::isCompositeType($namedType) ? $namedType : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\FieldNode:
                $parentType = $this->getParentType();
                $fieldDef = null;
                if ($parentType) {
                    $fieldDef = self::getFieldDefinition($schema, $parentType, $node);
                }
                $fieldType = null;
                if ($fieldDef) {
                    $fieldType = $fieldDef->getType();
                }
                $this->fieldDefStack[] = $fieldDef;
                $this->typeStack[] = \Builderius\GraphQL\Type\Definition\Type::isOutputType($fieldType) ? $fieldType : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\DirectiveNode:
                $this->directive = $schema->getDirective($node->name->value);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\OperationDefinitionNode:
                $type = null;
                if ($node->operation === 'query') {
                    $type = $schema->getQueryType();
                } elseif ($node->operation === 'mutation') {
                    $type = $schema->getMutationType();
                } elseif ($node->operation === 'subscription') {
                    $type = $schema->getSubscriptionType();
                }
                $this->typeStack[] = \Builderius\GraphQL\Type\Definition\Type::isOutputType($type) ? $type : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode:
            case $node instanceof \Builderius\GraphQL\Language\AST\FragmentDefinitionNode:
                $typeConditionNode = $node->typeCondition;
                $outputType = $typeConditionNode ? self::typeFromAST($schema, $typeConditionNode) : \Builderius\GraphQL\Type\Definition\Type::getNamedType($this->getType());
                $this->typeStack[] = \Builderius\GraphQL\Type\Definition\Type::isOutputType($outputType) ? $outputType : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\VariableDefinitionNode:
                $inputType = self::typeFromAST($schema, $node->type);
                $this->inputTypeStack[] = \Builderius\GraphQL\Type\Definition\Type::isInputType($inputType) ? $inputType : null;
                // push
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\ArgumentNode:
                $fieldOrDirective = $this->getDirective() ?? $this->getFieldDef();
                $argDef = $argType = null;
                if ($fieldOrDirective) {
                    /** @var FieldArgument $argDef */
                    $argDef = \Builderius\GraphQL\Utils\Utils::find($fieldOrDirective->args, static function ($arg) use($node) : bool {
                        return $arg->name === $node->name->value;
                    });
                    if ($argDef !== null) {
                        $argType = $argDef->getType();
                    }
                }
                $this->argument = $argDef;
                $this->defaultValueStack[] = $argDef && $argDef->defaultValueExists() ? $argDef->defaultValue : \Builderius\GraphQL\Utils\Utils::undefined();
                $this->inputTypeStack[] = \Builderius\GraphQL\Type\Definition\Type::isInputType($argType) ? $argType : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\ListValueNode:
                $type = $this->getInputType();
                $listType = $type === null ? null : \Builderius\GraphQL\Type\Definition\Type::getNullableType($type);
                $itemType = $listType instanceof \Builderius\GraphQL\Type\Definition\ListOfType ? $listType->getWrappedType() : $listType;
                // List positions never have a default value.
                $this->defaultValueStack[] = \Builderius\GraphQL\Utils\Utils::undefined();
                $this->inputTypeStack[] = \Builderius\GraphQL\Type\Definition\Type::isInputType($itemType) ? $itemType : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\ObjectFieldNode:
                $objectType = \Builderius\GraphQL\Type\Definition\Type::getNamedType($this->getInputType());
                $fieldType = null;
                $inputField = null;
                $inputFieldType = null;
                if ($objectType instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                    $tmp = $objectType->getFields();
                    $inputField = $tmp[$node->name->value] ?? null;
                    $inputFieldType = $inputField ? $inputField->getType() : null;
                }
                $this->defaultValueStack[] = $inputField && $inputField->defaultValueExists() ? $inputField->defaultValue : \Builderius\GraphQL\Utils\Utils::undefined();
                $this->inputTypeStack[] = \Builderius\GraphQL\Type\Definition\Type::isInputType($inputFieldType) ? $inputFieldType : null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\EnumValueNode:
                $enumType = \Builderius\GraphQL\Type\Definition\Type::getNamedType($this->getInputType());
                $enumValue = null;
                if ($enumType instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                    $this->enumValue = $enumType->getValue($node->value);
                }
                $this->enumValue = $enumValue;
                break;
        }
    }
    /**
     * @return (Type & OutputType) | null
     */
    public function getType() : ?\Builderius\GraphQL\Type\Definition\OutputType
    {
        return $this->typeStack[\count($this->typeStack) - 1] ?? null;
    }
    /**
     * @return (CompositeType & Type) | null
     */
    public function getParentType() : ?\Builderius\GraphQL\Type\Definition\CompositeType
    {
        return $this->parentTypeStack[\count($this->parentTypeStack) - 1] ?? null;
    }
    /**
     * Not exactly the same as the executor's definition of getFieldDef, in this
     * statically evaluated environment we do not always have an Object type,
     * and need to handle Interface and Union types.
     */
    private static function getFieldDefinition(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\Type $parentType, \Builderius\GraphQL\Language\AST\FieldNode $fieldNode) : ?\Builderius\GraphQL\Type\Definition\FieldDefinition
    {
        $name = $fieldNode->name->value;
        $schemaMeta = \Builderius\GraphQL\Type\Introspection::schemaMetaFieldDef();
        if ($name === $schemaMeta->name && $schema->getQueryType() === $parentType) {
            return $schemaMeta;
        }
        $typeMeta = \Builderius\GraphQL\Type\Introspection::typeMetaFieldDef();
        if ($name === $typeMeta->name && $schema->getQueryType() === $parentType) {
            return $typeMeta;
        }
        $typeNameMeta = \Builderius\GraphQL\Type\Introspection::typeNameMetaFieldDef();
        if ($name === $typeNameMeta->name && $parentType instanceof \Builderius\GraphQL\Type\Definition\CompositeType) {
            return $typeNameMeta;
        }
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $parentType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
            $fields = $parentType->getFields();
            return $fields[$name] ?? null;
        }
        return null;
    }
    /**
     * @param NamedTypeNode|ListTypeNode|NonNullTypeNode $inputTypeNode
     *
     * @throws InvariantViolation
     */
    public static function typeFromAST(\Builderius\GraphQL\Type\Schema $schema, $inputTypeNode) : ?\Builderius\GraphQL\Type\Definition\Type
    {
        return \Builderius\GraphQL\Utils\AST::typeFromAST($schema, $inputTypeNode);
    }
    public function getDirective() : ?\Builderius\GraphQL\Type\Definition\Directive
    {
        return $this->directive;
    }
    public function getFieldDef() : ?\Builderius\GraphQL\Type\Definition\FieldDefinition
    {
        return $this->fieldDefStack[\count($this->fieldDefStack) - 1] ?? null;
    }
    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValueStack[\count($this->defaultValueStack) - 1] ?? null;
    }
    /**
     * @return (Type & InputType) | null
     */
    public function getInputType() : ?\Builderius\GraphQL\Type\Definition\InputType
    {
        return $this->inputTypeStack[\count($this->inputTypeStack) - 1] ?? null;
    }
    public function leave(\Builderius\GraphQL\Language\AST\Node $node)
    {
        switch (\true) {
            case $node instanceof \Builderius\GraphQL\Language\AST\SelectionSetNode:
                \array_pop($this->parentTypeStack);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\FieldNode:
                \array_pop($this->fieldDefStack);
                \array_pop($this->typeStack);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\DirectiveNode:
                $this->directive = null;
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\OperationDefinitionNode:
            case $node instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode:
            case $node instanceof \Builderius\GraphQL\Language\AST\FragmentDefinitionNode:
                \array_pop($this->typeStack);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\VariableDefinitionNode:
                \array_pop($this->inputTypeStack);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\ArgumentNode:
                $this->argument = null;
                \array_pop($this->defaultValueStack);
                \array_pop($this->inputTypeStack);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\ListValueNode:
            case $node instanceof \Builderius\GraphQL\Language\AST\ObjectFieldNode:
                \array_pop($this->defaultValueStack);
                \array_pop($this->inputTypeStack);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\EnumValueNode:
                $this->enumValue = null;
                break;
        }
    }
}
