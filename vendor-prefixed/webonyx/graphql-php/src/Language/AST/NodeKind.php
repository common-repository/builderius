<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class NodeKind
{
    // constants from language/kinds.js:
    const NAME = 'Name';
    // Document
    const DOCUMENT = 'Document';
    const OPERATION_DEFINITION = 'OperationDefinition';
    const VARIABLE_DEFINITION = 'VariableDefinition';
    const VARIABLE = 'Variable';
    const SELECTION_SET = 'SelectionSet';
    const FIELD = 'Field';
    const ARGUMENT = 'Argument';
    // Fragments
    const FRAGMENT_SPREAD = 'FragmentSpread';
    const INLINE_FRAGMENT = 'InlineFragment';
    const FRAGMENT_DEFINITION = 'FragmentDefinition';
    // Values
    const INT = 'IntValue';
    const FLOAT = 'FloatValue';
    const STRING = 'StringValue';
    const BOOLEAN = 'BooleanValue';
    const ENUM = 'EnumValue';
    const NULL = 'NullValue';
    const LST = 'ListValue';
    const OBJECT = 'ObjectValue';
    const OBJECT_FIELD = 'ObjectField';
    // Directives
    const DIRECTIVE = 'Directive';
    // Types
    const NAMED_TYPE = 'NamedType';
    const LIST_TYPE = 'ListType';
    const NON_NULL_TYPE = 'NonNullType';
    // Type System Definitions
    const SCHEMA_DEFINITION = 'SchemaDefinition';
    const OPERATION_TYPE_DEFINITION = 'OperationTypeDefinition';
    // Type Definitions
    const SCALAR_TYPE_DEFINITION = 'ScalarTypeDefinition';
    const OBJECT_TYPE_DEFINITION = 'ObjectTypeDefinition';
    const FIELD_DEFINITION = 'FieldDefinition';
    const INPUT_VALUE_DEFINITION = 'InputValueDefinition';
    const INTERFACE_TYPE_DEFINITION = 'InterfaceTypeDefinition';
    const UNION_TYPE_DEFINITION = 'UnionTypeDefinition';
    const ENUM_TYPE_DEFINITION = 'EnumTypeDefinition';
    const ENUM_VALUE_DEFINITION = 'EnumValueDefinition';
    const INPUT_OBJECT_TYPE_DEFINITION = 'InputObjectTypeDefinition';
    // Type Extensions
    const SCALAR_TYPE_EXTENSION = 'ScalarTypeExtension';
    const OBJECT_TYPE_EXTENSION = 'ObjectTypeExtension';
    const INTERFACE_TYPE_EXTENSION = 'InterfaceTypeExtension';
    const UNION_TYPE_EXTENSION = 'UnionTypeExtension';
    const ENUM_TYPE_EXTENSION = 'EnumTypeExtension';
    const INPUT_OBJECT_TYPE_EXTENSION = 'InputObjectTypeExtension';
    // Directive Definitions
    const DIRECTIVE_DEFINITION = 'DirectiveDefinition';
    // Type System Extensions
    const SCHEMA_EXTENSION = 'SchemaExtension';
    /** @var string[] */
    public static $classMap = [
        self::NAME => \Builderius\GraphQL\Language\AST\NameNode::class,
        // Document
        self::DOCUMENT => \Builderius\GraphQL\Language\AST\DocumentNode::class,
        self::OPERATION_DEFINITION => \Builderius\GraphQL\Language\AST\OperationDefinitionNode::class,
        self::VARIABLE_DEFINITION => \Builderius\GraphQL\Language\AST\VariableDefinitionNode::class,
        self::VARIABLE => \Builderius\GraphQL\Language\AST\VariableNode::class,
        self::SELECTION_SET => \Builderius\GraphQL\Language\AST\SelectionSetNode::class,
        self::FIELD => \Builderius\GraphQL\Language\AST\FieldNode::class,
        self::ARGUMENT => \Builderius\GraphQL\Language\AST\ArgumentNode::class,
        // Fragments
        self::FRAGMENT_SPREAD => \Builderius\GraphQL\Language\AST\FragmentSpreadNode::class,
        self::INLINE_FRAGMENT => \Builderius\GraphQL\Language\AST\InlineFragmentNode::class,
        self::FRAGMENT_DEFINITION => \Builderius\GraphQL\Language\AST\FragmentDefinitionNode::class,
        // Values
        self::INT => \Builderius\GraphQL\Language\AST\IntValueNode::class,
        self::FLOAT => \Builderius\GraphQL\Language\AST\FloatValueNode::class,
        self::STRING => \Builderius\GraphQL\Language\AST\StringValueNode::class,
        self::BOOLEAN => \Builderius\GraphQL\Language\AST\BooleanValueNode::class,
        self::ENUM => \Builderius\GraphQL\Language\AST\EnumValueNode::class,
        self::NULL => \Builderius\GraphQL\Language\AST\NullValueNode::class,
        self::LST => \Builderius\GraphQL\Language\AST\ListValueNode::class,
        self::OBJECT => \Builderius\GraphQL\Language\AST\ObjectValueNode::class,
        self::OBJECT_FIELD => \Builderius\GraphQL\Language\AST\ObjectFieldNode::class,
        // Directives
        self::DIRECTIVE => \Builderius\GraphQL\Language\AST\DirectiveNode::class,
        // Types
        self::NAMED_TYPE => \Builderius\GraphQL\Language\AST\NamedTypeNode::class,
        self::LIST_TYPE => \Builderius\GraphQL\Language\AST\ListTypeNode::class,
        self::NON_NULL_TYPE => \Builderius\GraphQL\Language\AST\NonNullTypeNode::class,
        // Type System Definitions
        self::SCHEMA_DEFINITION => \Builderius\GraphQL\Language\AST\SchemaDefinitionNode::class,
        self::OPERATION_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\OperationTypeDefinitionNode::class,
        // Type Definitions
        self::SCALAR_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode::class,
        self::OBJECT_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode::class,
        self::FIELD_DEFINITION => \Builderius\GraphQL\Language\AST\FieldDefinitionNode::class,
        self::INPUT_VALUE_DEFINITION => \Builderius\GraphQL\Language\AST\InputValueDefinitionNode::class,
        self::INTERFACE_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode::class,
        self::UNION_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode::class,
        self::ENUM_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode::class,
        self::ENUM_VALUE_DEFINITION => \Builderius\GraphQL\Language\AST\EnumValueDefinitionNode::class,
        self::INPUT_OBJECT_TYPE_DEFINITION => \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode::class,
        // Type Extensions
        self::SCALAR_TYPE_EXTENSION => \Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode::class,
        self::OBJECT_TYPE_EXTENSION => \Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode::class,
        self::INTERFACE_TYPE_EXTENSION => \Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode::class,
        self::UNION_TYPE_EXTENSION => \Builderius\GraphQL\Language\AST\UnionTypeExtensionNode::class,
        self::ENUM_TYPE_EXTENSION => \Builderius\GraphQL\Language\AST\EnumTypeExtensionNode::class,
        self::INPUT_OBJECT_TYPE_EXTENSION => \Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode::class,
        // Directive Definitions
        self::DIRECTIVE_DEFINITION => \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode::class,
    ];
}
