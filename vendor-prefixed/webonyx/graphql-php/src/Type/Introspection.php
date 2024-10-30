<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type;

use Exception;
use Builderius\GraphQL\GraphQL;
use Builderius\GraphQL\Language\DirectiveLocation;
use Builderius\GraphQL\Language\Printer;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\FieldArgument;
use Builderius\GraphQL\Type\Definition\FieldDefinition;
use Builderius\GraphQL\Type\Definition\InputObjectField;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Definition\UnionType;
use Builderius\GraphQL\Type\Definition\WrappingType;
use Builderius\GraphQL\Utils\AST;
use Builderius\GraphQL\Utils\Utils;
use function array_filter;
use function array_key_exists;
use function array_merge;
use function array_values;
use function is_bool;
use function method_exists;
use function trigger_error;
use const E_USER_DEPRECATED;
class Introspection
{
    const SCHEMA_FIELD_NAME = '__schema';
    const TYPE_FIELD_NAME = '__type';
    const TYPE_NAME_FIELD_NAME = '__typename';
    /** @var array<string, mixed> */
    private static $map = [];
    /**
     * @param array<string, bool> $options
     *      Available options:
     *      - descriptions
     *        Whether to include descriptions in the introspection result.
     *        Default: true
     *      - directiveIsRepeatable
     *        Whether to include `isRepeatable` flag on directives.
     *        Default: false
     *
     * @return string
     *
     * @api
     */
    public static function getIntrospectionQuery(array $options = [])
    {
        $optionsWithDefaults = \array_merge(['descriptions' => \true, 'directiveIsRepeatable' => \false], $options);
        $descriptions = $optionsWithDefaults['descriptions'] ? 'description' : '';
        $directiveIsRepeatable = $optionsWithDefaults['directiveIsRepeatable'] ? 'isRepeatable' : '';
        return <<<EOD
  query IntrospectionQuery {
    __schema {
      queryType { name }
      mutationType { name }
      subscriptionType { name }
      types {
        ...FullType
      }
      directives {
        name
        {$descriptions}
        args {
          ...InputValue
        }
        {$directiveIsRepeatable}
        locations
      }
    }
  }

  fragment FullType on __Type {
    kind
    name
    {$descriptions}
    fields(includeDeprecated: true) {
      name
      {$descriptions}
      args {
        ...InputValue
      }
      type {
        ...TypeRef
      }
      isDeprecated
      deprecationReason
    }
    inputFields {
      ...InputValue
    }
    interfaces {
      ...TypeRef
    }
    enumValues(includeDeprecated: true) {
      name
      {$descriptions}
      isDeprecated
      deprecationReason
    }
    possibleTypes {
      ...TypeRef
    }
  }

  fragment InputValue on __InputValue {
    name
    {$descriptions}
    type { ...TypeRef }
    defaultValue
  }

  fragment TypeRef on __Type {
    kind
    name
    ofType {
      kind
      name
      ofType {
        kind
        name
        ofType {
          kind
          name
          ofType {
            kind
            name
            ofType {
              kind
              name
              ofType {
                kind
                name
                ofType {
                  kind
                  name
                }
              }
            }
          }
        }
      }
    }
  }
EOD;
    }
    /**
     * @param Type $type
     *
     * @return bool
     */
    public static function isIntrospectionType($type)
    {
        return \array_key_exists($type->name, self::getTypes());
    }
    public static function getTypes()
    {
        return ['__Schema' => self::_schema(), '__Type' => self::_type(), '__Directive' => self::_directive(), '__Field' => self::_field(), '__InputValue' => self::_inputValue(), '__EnumValue' => self::_enumValue(), '__TypeKind' => self::_typeKind(), '__DirectiveLocation' => self::_directiveLocation()];
    }
    /**
     * Build an introspection query from a Schema
     *
     * Introspection is useful for utilities that care about type and field
     * relationships, but do not need to traverse through those relationships.
     *
     * This is the inverse of BuildClientSchema::build(). The primary use case is outside
     * of the server context, for instance when doing schema comparisons.
     *
     * @param array<string, bool> $options
     *      Available options:
     *      - descriptions
     *        Whether to include `isRepeatable` flag on directives.
     *        Default: true
     *      - directiveIsRepeatable
     *        Whether to include descriptions in the introspection result.
     *        Default: true
     *
     * @return array<string, array<mixed>>|null
     *
     * @api
     */
    public static function fromSchema(\Builderius\GraphQL\Type\Schema $schema, array $options = []) : ?array
    {
        $optionsWithDefaults = \array_merge(['directiveIsRepeatable' => \true], $options);
        $result = \Builderius\GraphQL\GraphQL::executeQuery($schema, self::getIntrospectionQuery($optionsWithDefaults));
        return $result->data;
    }
    public static function _schema()
    {
        if (!isset(self::$map['__Schema'])) {
            self::$map['__Schema'] = new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => '__Schema', 'isIntrospection' => \true, 'description' => 'A GraphQL Schema defines the capabilities of a GraphQL ' . 'server. It exposes all available types and directives on ' . 'the server, as well as the entry points for query, mutation, and ' . 'subscription operations.', 'fields' => ['types' => ['description' => 'A list of all types supported by this server.', 'type' => new \Builderius\GraphQL\Type\Definition\NonNull(new \Builderius\GraphQL\Type\Definition\ListOfType(new \Builderius\GraphQL\Type\Definition\NonNull(self::_type()))), 'resolve' => static function (\Builderius\GraphQL\Type\Schema $schema) : array {
                return \array_values($schema->getTypeMap());
            }], 'queryType' => ['description' => 'The type that query operations will be rooted at.', 'type' => new \Builderius\GraphQL\Type\Definition\NonNull(self::_type()), 'resolve' => static function (\Builderius\GraphQL\Type\Schema $schema) : ?ObjectType {
                return $schema->getQueryType();
            }], 'mutationType' => ['description' => 'If this server supports mutation, the type that ' . 'mutation operations will be rooted at.', 'type' => self::_type(), 'resolve' => static function (\Builderius\GraphQL\Type\Schema $schema) : ?ObjectType {
                return $schema->getMutationType();
            }], 'subscriptionType' => ['description' => 'If this server support subscription, the type that subscription operations will be rooted at.', 'type' => self::_type(), 'resolve' => static function (\Builderius\GraphQL\Type\Schema $schema) : ?ObjectType {
                return $schema->getSubscriptionType();
            }], 'directives' => ['description' => 'A list of all directives supported by this server.', 'type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_directive()))), 'resolve' => static function (\Builderius\GraphQL\Type\Schema $schema) : array {
                return $schema->getDirectives();
            }]]]);
        }
        return self::$map['__Schema'];
    }
    public static function _type()
    {
        if (!isset(self::$map['__Type'])) {
            self::$map['__Type'] = new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => '__Type', 'isIntrospection' => \true, 'description' => 'The fundamental unit of any GraphQL Schema is the type. There are ' . 'many kinds of types in GraphQL as represented by the `__TypeKind` enum.' . "\n\n" . 'Depending on the kind of a type, certain fields describe ' . 'information about that type. Scalar types provide no information ' . 'beyond a name and description, while Enum types provide their values. ' . 'Object and Interface types provide the fields they describe. Abstract ' . 'types, Union and Interface, provide the Object types possible ' . 'at runtime. List and NonNull types compose other types.', 'fields' => static function () {
                return ['kind' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(self::_typeKind()), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\Type $type) {
                    switch (\true) {
                        case $type instanceof \Builderius\GraphQL\Type\Definition\ListOfType:
                            return \Builderius\GraphQL\Type\TypeKind::LIST;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\NonNull:
                            return \Builderius\GraphQL\Type\TypeKind::NON_NULL;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\ScalarType:
                            return \Builderius\GraphQL\Type\TypeKind::SCALAR;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\ObjectType:
                            return \Builderius\GraphQL\Type\TypeKind::OBJECT;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\EnumType:
                            return \Builderius\GraphQL\Type\TypeKind::ENUM;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType:
                            return \Builderius\GraphQL\Type\TypeKind::INPUT_OBJECT;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType:
                            return \Builderius\GraphQL\Type\TypeKind::INTERFACE;
                        case $type instanceof \Builderius\GraphQL\Type\Definition\UnionType:
                            return \Builderius\GraphQL\Type\TypeKind::UNION;
                        default:
                            throw new \Exception('Unknown kind of type: ' . \Builderius\GraphQL\Utils\Utils::printSafe($type));
                    }
                }], 'name' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function ($obj) {
                    return $obj->name;
                }], 'description' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function ($obj) {
                    return $obj->description;
                }], 'fields' => ['type' => \Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_field())), 'args' => ['includeDeprecated' => ['type' => \Builderius\GraphQL\Type\Definition\Type::boolean(), 'defaultValue' => \false]], 'resolve' => static function (\Builderius\GraphQL\Type\Definition\Type $type, $args) {
                    if ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType || $type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
                        $fields = $type->getFields();
                        if (!($args['includeDeprecated'] ?? \false)) {
                            $fields = \array_filter($fields, static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : bool {
                                return !$field->deprecationReason;
                            });
                        }
                        return \array_values($fields);
                    }
                    return null;
                }], 'interfaces' => ['type' => \Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_type())), 'resolve' => static function ($type) : ?array {
                    if ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                        return $type->getInterfaces();
                    }
                    return null;
                }], 'possibleTypes' => ['type' => \Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_type())), 'resolve' => static function ($type, $args, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info) : ?array {
                    if ($type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType || $type instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
                        return $info->schema->getPossibleTypes($type);
                    }
                    return null;
                }], 'enumValues' => ['type' => \Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_enumValue())), 'args' => ['includeDeprecated' => ['type' => \Builderius\GraphQL\Type\Definition\Type::boolean(), 'defaultValue' => \false]], 'resolve' => static function ($type, $args) {
                    if ($type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                        $values = \array_values($type->getValues());
                        if (!($args['includeDeprecated'] ?? \false)) {
                            $values = \array_filter($values, static function ($value) : bool {
                                return !$value->deprecationReason;
                            });
                        }
                        return $values;
                    }
                    return null;
                }], 'inputFields' => ['type' => \Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_inputValue())), 'resolve' => static function ($type) : ?array {
                    if ($type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                        return \array_values($type->getFields());
                    }
                    return null;
                }], 'ofType' => ['type' => self::_type(), 'resolve' => static function ($type) : ?Type {
                    if ($type instanceof \Builderius\GraphQL\Type\Definition\WrappingType) {
                        return $type->getWrappedType();
                    }
                    return null;
                }]];
            }]);
        }
        return self::$map['__Type'];
    }
    public static function _typeKind()
    {
        if (!isset(self::$map['__TypeKind'])) {
            self::$map['__TypeKind'] = new \Builderius\GraphQL\Type\Definition\EnumType(['name' => '__TypeKind', 'isIntrospection' => \true, 'description' => 'An enum describing what kind of type a given `__Type` is.', 'values' => ['SCALAR' => ['value' => \Builderius\GraphQL\Type\TypeKind::SCALAR, 'description' => 'Indicates this type is a scalar.'], 'OBJECT' => ['value' => \Builderius\GraphQL\Type\TypeKind::OBJECT, 'description' => 'Indicates this type is an object. `fields` and `interfaces` are valid fields.'], 'INTERFACE' => ['value' => \Builderius\GraphQL\Type\TypeKind::INTERFACE, 'description' => 'Indicates this type is an interface. `fields` and `possibleTypes` are valid fields.'], 'UNION' => ['value' => \Builderius\GraphQL\Type\TypeKind::UNION, 'description' => 'Indicates this type is a union. `possibleTypes` is a valid field.'], 'ENUM' => ['value' => \Builderius\GraphQL\Type\TypeKind::ENUM, 'description' => 'Indicates this type is an enum. `enumValues` is a valid field.'], 'INPUT_OBJECT' => ['value' => \Builderius\GraphQL\Type\TypeKind::INPUT_OBJECT, 'description' => 'Indicates this type is an input object. `inputFields` is a valid field.'], 'LIST' => ['value' => \Builderius\GraphQL\Type\TypeKind::LIST, 'description' => 'Indicates this type is a list. `ofType` is a valid field.'], 'NON_NULL' => ['value' => \Builderius\GraphQL\Type\TypeKind::NON_NULL, 'description' => 'Indicates this type is a non-null. `ofType` is a valid field.']]]);
        }
        return self::$map['__TypeKind'];
    }
    public static function _field()
    {
        if (!isset(self::$map['__Field'])) {
            self::$map['__Field'] = new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => '__Field', 'isIntrospection' => \true, 'description' => 'Object and Interface types are described by a list of Fields, each of ' . 'which has a name, potentially a list of arguments, and a return type.', 'fields' => static function () {
                return ['name' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::string()), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : string {
                    return $field->name;
                }], 'description' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : ?string {
                    return $field->description;
                }], 'args' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_inputValue()))), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : array {
                    return $field->args ?? [];
                }], 'type' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(self::_type()), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : Type {
                    return $field->getType();
                }], 'isDeprecated' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::boolean()), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : bool {
                    return (bool) $field->deprecationReason;
                }], 'deprecationReason' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\FieldDefinition $field) : ?string {
                    return $field->deprecationReason;
                }]];
            }]);
        }
        return self::$map['__Field'];
    }
    public static function _inputValue()
    {
        if (!isset(self::$map['__InputValue'])) {
            self::$map['__InputValue'] = new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => '__InputValue', 'isIntrospection' => \true, 'description' => 'Arguments provided to Fields or Directives and the input fields of an ' . 'InputObject are represented as Input Values which describe their type ' . 'and optionally a default value.', 'fields' => static function () {
                return ['name' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::string()), 'resolve' => static function ($inputValue) : string {
                    /** @var FieldArgument|InputObjectField $inputValue */
                    $inputValue = $inputValue;
                    return $inputValue->name;
                }], 'description' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function ($inputValue) : ?string {
                    /** @var FieldArgument|InputObjectField $inputValue */
                    $inputValue = $inputValue;
                    return $inputValue->description;
                }], 'type' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(self::_type()), 'resolve' => static function ($value) {
                    return \method_exists($value, 'getType') ? $value->getType() : $value->type;
                }], 'defaultValue' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'description' => 'A GraphQL-formatted string representing the default value for this input value.', 'resolve' => static function ($inputValue) : ?string {
                    /** @var FieldArgument|InputObjectField $inputValue */
                    $inputValue = $inputValue;
                    return !$inputValue->defaultValueExists() ? null : \Builderius\GraphQL\Language\Printer::doPrint(\Builderius\GraphQL\Utils\AST::astFromValue($inputValue->defaultValue, $inputValue->getType()));
                }]];
            }]);
        }
        return self::$map['__InputValue'];
    }
    public static function _enumValue()
    {
        if (!isset(self::$map['__EnumValue'])) {
            self::$map['__EnumValue'] = new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => '__EnumValue', 'isIntrospection' => \true, 'description' => 'One possible value for a given Enum. Enum values are unique values, not ' . 'a placeholder for a string or numeric value. However an Enum value is ' . 'returned in a JSON response as a string.', 'fields' => ['name' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::string()), 'resolve' => static function ($enumValue) {
                return $enumValue->name;
            }], 'description' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function ($enumValue) {
                return $enumValue->description;
            }], 'isDeprecated' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::boolean()), 'resolve' => static function ($enumValue) : bool {
                return (bool) $enumValue->deprecationReason;
            }], 'deprecationReason' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function ($enumValue) {
                return $enumValue->deprecationReason;
            }]]]);
        }
        return self::$map['__EnumValue'];
    }
    public static function _directive()
    {
        if (!isset(self::$map['__Directive'])) {
            self::$map['__Directive'] = new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => '__Directive', 'isIntrospection' => \true, 'description' => 'A Directive provides a way to describe alternate runtime execution and ' . 'type validation behavior in a GraphQL document.' . "\n\nIn some cases, you need to provide options to alter GraphQL's " . 'execution behavior in ways field arguments will not suffice, such as ' . 'conditionally including or skipping a field. Directives provide this by ' . 'describing additional information to the executor.', 'fields' => ['name' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::string()), 'resolve' => static function ($obj) {
                return $obj->name;
            }], 'description' => ['type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'resolve' => static function ($obj) {
                return $obj->description;
            }], 'args' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_inputValue()))), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\Directive $directive) {
                return $directive->args ?? [];
            }], 'isRepeatable' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::boolean()), 'resolve' => static function (\Builderius\GraphQL\Type\Definition\Directive $directive) : bool {
                return $directive->isRepeatable;
            }], 'locations' => ['type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::listOf(\Builderius\GraphQL\Type\Definition\Type::nonNull(self::_directiveLocation()))), 'resolve' => static function ($obj) {
                return $obj->locations;
            }]]]);
        }
        return self::$map['__Directive'];
    }
    public static function _directiveLocation()
    {
        if (!isset(self::$map['__DirectiveLocation'])) {
            self::$map['__DirectiveLocation'] = new \Builderius\GraphQL\Type\Definition\EnumType(['name' => '__DirectiveLocation', 'isIntrospection' => \true, 'description' => 'A Directive can be adjacent to many parts of the GraphQL language, a ' . '__DirectiveLocation describes one such possible adjacencies.', 'values' => ['QUERY' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::QUERY, 'description' => 'Location adjacent to a query operation.'], 'MUTATION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::MUTATION, 'description' => 'Location adjacent to a mutation operation.'], 'SUBSCRIPTION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::SUBSCRIPTION, 'description' => 'Location adjacent to a subscription operation.'], 'FIELD' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::FIELD, 'description' => 'Location adjacent to a field.'], 'FRAGMENT_DEFINITION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::FRAGMENT_DEFINITION, 'description' => 'Location adjacent to a fragment definition.'], 'FRAGMENT_SPREAD' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::FRAGMENT_SPREAD, 'description' => 'Location adjacent to a fragment spread.'], 'INLINE_FRAGMENT' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::INLINE_FRAGMENT, 'description' => 'Location adjacent to an inline fragment.'], 'VARIABLE_DEFINITION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::VARIABLE_DEFINITION, 'description' => 'Location adjacent to a variable definition.'], 'SCHEMA' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::SCHEMA, 'description' => 'Location adjacent to a schema definition.'], 'SCALAR' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::SCALAR, 'description' => 'Location adjacent to a scalar definition.'], 'OBJECT' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::OBJECT, 'description' => 'Location adjacent to an object type definition.'], 'FIELD_DEFINITION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::FIELD_DEFINITION, 'description' => 'Location adjacent to a field definition.'], 'ARGUMENT_DEFINITION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::ARGUMENT_DEFINITION, 'description' => 'Location adjacent to an argument definition.'], 'INTERFACE' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::IFACE, 'description' => 'Location adjacent to an interface definition.'], 'UNION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::UNION, 'description' => 'Location adjacent to a union definition.'], 'ENUM' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::ENUM, 'description' => 'Location adjacent to an enum definition.'], 'ENUM_VALUE' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::ENUM_VALUE, 'description' => 'Location adjacent to an enum value definition.'], 'INPUT_OBJECT' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::INPUT_OBJECT, 'description' => 'Location adjacent to an input object type definition.'], 'INPUT_FIELD_DEFINITION' => ['value' => \Builderius\GraphQL\Language\DirectiveLocation::INPUT_FIELD_DEFINITION, 'description' => 'Location adjacent to an input object field definition.']]]);
        }
        return self::$map['__DirectiveLocation'];
    }
    public static function schemaMetaFieldDef() : \Builderius\GraphQL\Type\Definition\FieldDefinition
    {
        if (!isset(self::$map[self::SCHEMA_FIELD_NAME])) {
            self::$map[self::SCHEMA_FIELD_NAME] = \Builderius\GraphQL\Type\Definition\FieldDefinition::create(['name' => self::SCHEMA_FIELD_NAME, 'type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(self::_schema()), 'description' => 'Access the current type schema of this server.', 'args' => [], 'resolve' => static function ($source, $args, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info) : Schema {
                return $info->schema;
            }]);
        }
        return self::$map[self::SCHEMA_FIELD_NAME];
    }
    public static function typeMetaFieldDef() : \Builderius\GraphQL\Type\Definition\FieldDefinition
    {
        if (!isset(self::$map[self::TYPE_FIELD_NAME])) {
            self::$map[self::TYPE_FIELD_NAME] = \Builderius\GraphQL\Type\Definition\FieldDefinition::create(['name' => self::TYPE_FIELD_NAME, 'type' => self::_type(), 'description' => 'Request the type information of a single type.', 'args' => [['name' => 'name', 'type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::string())]], 'resolve' => static function ($source, $args, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info) : Type {
                return $info->schema->getType($args['name']);
            }]);
        }
        return self::$map[self::TYPE_FIELD_NAME];
    }
    public static function typeNameMetaFieldDef() : \Builderius\GraphQL\Type\Definition\FieldDefinition
    {
        if (!isset(self::$map[self::TYPE_NAME_FIELD_NAME])) {
            self::$map[self::TYPE_NAME_FIELD_NAME] = \Builderius\GraphQL\Type\Definition\FieldDefinition::create(['name' => self::TYPE_NAME_FIELD_NAME, 'type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::string()), 'description' => 'The name of the current Object type at runtime.', 'args' => [], 'resolve' => static function ($source, $args, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info) : string {
                return $info->parentType->name;
            }]);
        }
        return self::$map[self::TYPE_NAME_FIELD_NAME];
    }
}
