<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Utils;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\Parser;
use Builderius\GraphQL\Type\Definition\CustomScalarType;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NamedType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\NullableType;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\OutputType;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Definition\UnionType;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Type\SchemaConfig;
use Builderius\GraphQL\Type\TypeKind;
use function array_key_exists;
use function array_map;
use function array_merge;
use function json_encode;
class BuildClientSchema
{
    /** @var array<string, mixed[]> */
    private $introspection;
    /** @var array<string, bool> */
    private $options;
    /** @var array<string, NamedType&Type> */
    private $typeMap;
    /**
     * @param array<string, mixed[]> $introspectionQuery
     * @param array<string, bool>    $options
     */
    public function __construct(array $introspectionQuery, array $options = [])
    {
        $this->introspection = $introspectionQuery;
        $this->options = $options;
    }
    /**
     * Build a schema for use by client tools.
     *
     * Given the result of a client running the introspection query, creates and
     * returns a \GraphQL\Type\Schema instance which can be then used with all graphql-php
     * tools, but cannot be used to execute a query, as introspection does not
     * represent the "resolver", "parse" or "serialize" functions or any other
     * server-internal mechanisms.
     *
     * This function expects a complete introspection result. Don't forget to check
     * the "errors" field of a server response before calling this function.
     *
     * Accepts options as a third argument:
     *
     *    - assumeValid:
     *          When building a schema from a GraphQL service's introspection result, it
     *          might be safe to assume the schema is valid. Set to true to assume the
     *          produced schema is valid.
     *
     *          Default: false
     *
     * @param array<string, mixed[]> $introspectionQuery
     * @param array<string, bool>    $options
     *
     * @api
     */
    public static function build(array $introspectionQuery, array $options = []) : \Builderius\GraphQL\Type\Schema
    {
        $builder = new self($introspectionQuery, $options);
        return $builder->buildSchema();
    }
    public function buildSchema() : \Builderius\GraphQL\Type\Schema
    {
        if (!\array_key_exists('__schema', $this->introspection)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Invalid or incomplete introspection result. Ensure that you are passing "data" property of introspection response and no "errors" was returned alongside: ' . \json_encode($this->introspection) . '.');
        }
        $schemaIntrospection = $this->introspection['__schema'];
        $this->typeMap = \Builderius\GraphQL\Utils\Utils::keyValMap($schemaIntrospection['types'], static function (array $typeIntrospection) {
            return $typeIntrospection['name'];
        }, function (array $typeIntrospection) : NamedType {
            return $this->buildType($typeIntrospection);
        });
        $builtInTypes = \array_merge(\Builderius\GraphQL\Type\Definition\Type::getStandardTypes(), \Builderius\GraphQL\Type\Introspection::getTypes());
        foreach ($builtInTypes as $name => $type) {
            if (!isset($this->typeMap[$name])) {
                continue;
            }
            $this->typeMap[$name] = $type;
        }
        $queryType = isset($schemaIntrospection['queryType']) ? $this->getObjectType($schemaIntrospection['queryType']) : null;
        $mutationType = isset($schemaIntrospection['mutationType']) ? $this->getObjectType($schemaIntrospection['mutationType']) : null;
        $subscriptionType = isset($schemaIntrospection['subscriptionType']) ? $this->getObjectType($schemaIntrospection['subscriptionType']) : null;
        $directives = isset($schemaIntrospection['directives']) ? \array_map([$this, 'buildDirective'], $schemaIntrospection['directives']) : [];
        $schemaConfig = new \Builderius\GraphQL\Type\SchemaConfig();
        $schemaConfig->setQuery($queryType)->setMutation($mutationType)->setSubscription($subscriptionType)->setTypes($this->typeMap)->setDirectives($directives)->setAssumeValid(isset($this->options) && isset($this->options['assumeValid']) && $this->options['assumeValid']);
        return new \Builderius\GraphQL\Type\Schema($schemaConfig);
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getType(array $typeRef) : \Builderius\GraphQL\Type\Definition\Type
    {
        if (isset($typeRef['kind'])) {
            if ($typeRef['kind'] === \Builderius\GraphQL\Type\TypeKind::LIST) {
                if (!isset($typeRef['ofType'])) {
                    throw new \Builderius\GraphQL\Error\InvariantViolation('Decorated type deeper than introspection query.');
                }
                return new \Builderius\GraphQL\Type\Definition\ListOfType($this->getType($typeRef['ofType']));
            }
            if ($typeRef['kind'] === \Builderius\GraphQL\Type\TypeKind::NON_NULL) {
                if (!isset($typeRef['ofType'])) {
                    throw new \Builderius\GraphQL\Error\InvariantViolation('Decorated type deeper than introspection query.');
                }
                /** @var NullableType $nullableType */
                $nullableType = $this->getType($typeRef['ofType']);
                return new \Builderius\GraphQL\Type\Definition\NonNull($nullableType);
            }
        }
        if (!isset($typeRef['name'])) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Unknown type reference: ' . \json_encode($typeRef) . '.');
        }
        return $this->getNamedType($typeRef['name']);
    }
    /**
     * @return NamedType&Type
     */
    private function getNamedType(string $typeName) : \Builderius\GraphQL\Type\Definition\NamedType
    {
        if (!isset($this->typeMap[$typeName])) {
            throw new \Builderius\GraphQL\Error\InvariantViolation("Invalid or incomplete schema, unknown type: {$typeName}. Ensure that a full introspection query is used in order to build a client schema.");
        }
        return $this->typeMap[$typeName];
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getInputType(array $typeRef) : \Builderius\GraphQL\Type\Definition\InputType
    {
        $type = $this->getType($typeRef);
        if ($type instanceof \Builderius\GraphQL\Type\Definition\InputType) {
            return $type;
        }
        throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection must provide input type for arguments, but received: ' . \json_encode($type) . '.');
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getOutputType(array $typeRef) : \Builderius\GraphQL\Type\Definition\OutputType
    {
        $type = $this->getType($typeRef);
        if ($type instanceof \Builderius\GraphQL\Type\Definition\OutputType) {
            return $type;
        }
        throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection must provide output type for fields, but received: ' . \json_encode($type) . '.');
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getObjectType(array $typeRef) : \Builderius\GraphQL\Type\Definition\ObjectType
    {
        $type = $this->getType($typeRef);
        return \Builderius\GraphQL\Type\Definition\ObjectType::assertObjectType($type);
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    public function getInterfaceType(array $typeRef) : \Builderius\GraphQL\Type\Definition\InterfaceType
    {
        $type = $this->getType($typeRef);
        return \Builderius\GraphQL\Type\Definition\InterfaceType::assertInterfaceType($type);
    }
    /**
     * @param array<string, mixed> $type
     */
    private function buildType(array $type) : \Builderius\GraphQL\Type\Definition\NamedType
    {
        if (\array_key_exists('name', $type) && \array_key_exists('kind', $type)) {
            switch ($type['kind']) {
                case \Builderius\GraphQL\Type\TypeKind::SCALAR:
                    return $this->buildScalarDef($type);
                case \Builderius\GraphQL\Type\TypeKind::OBJECT:
                    return $this->buildObjectDef($type);
                case \Builderius\GraphQL\Type\TypeKind::INTERFACE:
                    return $this->buildInterfaceDef($type);
                case \Builderius\GraphQL\Type\TypeKind::UNION:
                    return $this->buildUnionDef($type);
                case \Builderius\GraphQL\Type\TypeKind::ENUM:
                    return $this->buildEnumDef($type);
                case \Builderius\GraphQL\Type\TypeKind::INPUT_OBJECT:
                    return $this->buildInputObjectDef($type);
            }
        }
        throw new \Builderius\GraphQL\Error\InvariantViolation('Invalid or incomplete introspection result. Ensure that a full introspection query is used in order to build a client schema: ' . \json_encode($type) . '.');
    }
    /**
     * @param array<string, string> $scalar
     */
    private function buildScalarDef(array $scalar) : \Builderius\GraphQL\Type\Definition\ScalarType
    {
        return new \Builderius\GraphQL\Type\Definition\CustomScalarType(['name' => $scalar['name'], 'description' => $scalar['description'], 'serialize' => static function ($value) : string {
            return (string) $value;
        }]);
    }
    /**
     * @param array<string, mixed> $object
     */
    private function buildObjectDef(array $object) : \Builderius\GraphQL\Type\Definition\ObjectType
    {
        if (!\array_key_exists('interfaces', $object)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing interfaces: ' . \json_encode($object) . '.');
        }
        return new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => $object['name'], 'description' => $object['description'], 'interfaces' => function () use($object) : array {
            return \array_map(
                [$this, 'getInterfaceType'],
                // Legacy support for interfaces with null as interfaces field
                $object['interfaces'] ?? []
            );
        }, 'fields' => function () use($object) {
            return $this->buildFieldDefMap($object);
        }]);
    }
    /**
     * @param array<string, mixed> $interface
     */
    private function buildInterfaceDef(array $interface) : \Builderius\GraphQL\Type\Definition\InterfaceType
    {
        return new \Builderius\GraphQL\Type\Definition\InterfaceType(['name' => $interface['name'], 'description' => $interface['description'], 'fields' => function () use($interface) {
            return $this->buildFieldDefMap($interface);
        }]);
    }
    /**
     * @param array<string, string|array<string>> $union
     */
    private function buildUnionDef(array $union) : \Builderius\GraphQL\Type\Definition\UnionType
    {
        if (!\array_key_exists('possibleTypes', $union)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing possibleTypes: ' . \json_encode($union) . '.');
        }
        return new \Builderius\GraphQL\Type\Definition\UnionType(['name' => $union['name'], 'description' => $union['description'], 'types' => function () use($union) : array {
            return \array_map([$this, 'getObjectType'], $union['possibleTypes']);
        }]);
    }
    /**
     * @param array<string, string|array<string, string>> $enum
     */
    private function buildEnumDef(array $enum) : \Builderius\GraphQL\Type\Definition\EnumType
    {
        if (!\array_key_exists('enumValues', $enum)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing enumValues: ' . \json_encode($enum) . '.');
        }
        return new \Builderius\GraphQL\Type\Definition\EnumType(['name' => $enum['name'], 'description' => $enum['description'], 'values' => \Builderius\GraphQL\Utils\Utils::keyValMap($enum['enumValues'], static function (array $enumValue) : string {
            return $enumValue['name'];
        }, static function (array $enumValue) : array {
            return ['description' => $enumValue['description'], 'deprecationReason' => $enumValue['deprecationReason']];
        })]);
    }
    /**
     * @param array<string, mixed> $inputObject
     */
    private function buildInputObjectDef(array $inputObject) : \Builderius\GraphQL\Type\Definition\InputObjectType
    {
        if (!\array_key_exists('inputFields', $inputObject)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing inputFields: ' . \json_encode($inputObject) . '.');
        }
        return new \Builderius\GraphQL\Type\Definition\InputObjectType(['name' => $inputObject['name'], 'description' => $inputObject['description'], 'fields' => function () use($inputObject) : array {
            return $this->buildInputValueDefMap($inputObject['inputFields']);
        }]);
    }
    /**
     * @param array<string, mixed> $typeIntrospection
     */
    private function buildFieldDefMap(array $typeIntrospection)
    {
        if (!\array_key_exists('fields', $typeIntrospection)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing fields: ' . \json_encode($typeIntrospection) . '.');
        }
        return \Builderius\GraphQL\Utils\Utils::keyValMap($typeIntrospection['fields'], static function (array $fieldIntrospection) : string {
            return $fieldIntrospection['name'];
        }, function (array $fieldIntrospection) : array {
            if (!\array_key_exists('args', $fieldIntrospection)) {
                throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing field args: ' . \json_encode($fieldIntrospection) . '.');
            }
            return ['description' => $fieldIntrospection['description'], 'deprecationReason' => $fieldIntrospection['deprecationReason'], 'type' => $this->getOutputType($fieldIntrospection['type']), 'args' => $this->buildInputValueDefMap($fieldIntrospection['args'])];
        });
    }
    /**
     * @param array<int, array<string, mixed>> $inputValueIntrospections
     *
     * @return array<string, array<string, mixed>>
     */
    private function buildInputValueDefMap(array $inputValueIntrospections) : array
    {
        return \Builderius\GraphQL\Utils\Utils::keyValMap($inputValueIntrospections, static function (array $inputValue) : string {
            return $inputValue['name'];
        }, [$this, 'buildInputValue']);
    }
    /**
     * @param array<string, mixed> $inputValueIntrospection
     *
     * @return array<string, mixed>
     */
    public function buildInputValue(array $inputValueIntrospection) : array
    {
        $type = $this->getInputType($inputValueIntrospection['type']);
        $inputValue = ['description' => $inputValueIntrospection['description'], 'type' => $type];
        if (isset($inputValueIntrospection['defaultValue'])) {
            $inputValue['defaultValue'] = \Builderius\GraphQL\Utils\AST::valueFromAST(\Builderius\GraphQL\Language\Parser::parseValue($inputValueIntrospection['defaultValue']), $type);
        }
        return $inputValue;
    }
    /**
     * @param array<string, mixed> $directive
     */
    public function buildDirective(array $directive) : \Builderius\GraphQL\Type\Definition\Directive
    {
        if (!\array_key_exists('args', $directive)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing directive args: ' . \json_encode($directive) . '.');
        }
        if (!\array_key_exists('locations', $directive)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Introspection result missing directive locations: ' . \json_encode($directive) . '.');
        }
        return new \Builderius\GraphQL\Type\Definition\Directive(['name' => $directive['name'], 'description' => $directive['description'], 'args' => $this->buildInputValueDefMap($directive['args']), 'isRepeatable' => $directive['isRepeatable'], 'locations' => $directive['locations']]);
    }
}
