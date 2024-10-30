<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Utils;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Executor\Values;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumValueDefinitionNode;
use Builderius\GraphQL\Language\AST\FieldDefinitionNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ListTypeNode;
use Builderius\GraphQL\Language\AST\NamedTypeNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NonNullTypeNode;
use Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\TypeNode;
use Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode;
use Builderius\GraphQL\Language\Token;
use Builderius\GraphQL\Type\Definition\CustomScalarType;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\FieldArgument;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Definition\UnionType;
use Throwable;
use function array_reverse;
use function implode;
use function is_array;
use function is_string;
use function sprintf;
class ASTDefinitionBuilder
{
    /** @var Node[] */
    private $typeDefinitionsMap;
    /** @var callable */
    private $typeConfigDecorator;
    /** @var bool[] */
    private $options;
    /** @var callable */
    private $resolveType;
    /** @var Type[] */
    private $cache;
    /**
     * @param Node[] $typeDefinitionsMap
     * @param bool[] $options
     */
    public function __construct(array $typeDefinitionsMap, $options, callable $resolveType, ?callable $typeConfigDecorator = null)
    {
        $this->typeDefinitionsMap = $typeDefinitionsMap;
        $this->typeConfigDecorator = $typeConfigDecorator;
        $this->options = $options;
        $this->resolveType = $resolveType;
        $this->cache = \Builderius\GraphQL\Type\Definition\Type::getAllBuiltInTypes();
    }
    public function buildDirective(\Builderius\GraphQL\Language\AST\DirectiveDefinitionNode $directiveNode)
    {
        return new \Builderius\GraphQL\Type\Definition\Directive(['name' => $directiveNode->name->value, 'description' => $this->getDescription($directiveNode), 'args' => isset($directiveNode->arguments) ? \Builderius\GraphQL\Type\Definition\FieldArgument::createMap($this->makeInputValues($directiveNode->arguments)) : null, 'isRepeatable' => $directiveNode->repeatable, 'locations' => \Builderius\GraphQL\Utils\Utils::map($directiveNode->locations, static function ($node) {
            return $node->value;
        }), 'astNode' => $directiveNode]);
    }
    /**
     * Given an ast node, returns its string description.
     */
    private function getDescription($node)
    {
        if ($node->description) {
            return $node->description->value;
        }
        if (isset($this->options['commentDescriptions'])) {
            $rawValue = $this->getLeadingCommentBlock($node);
            if ($rawValue !== null) {
                return \Builderius\GraphQL\Utils\BlockString::value("\n" . $rawValue);
            }
        }
        return null;
    }
    private function getLeadingCommentBlock($node)
    {
        $loc = $node->loc;
        if (!$loc || !$loc->startToken) {
            return null;
        }
        $comments = [];
        $token = $loc->startToken->prev;
        while ($token && $token->kind === \Builderius\GraphQL\Language\Token::COMMENT && $token->next && $token->prev && $token->line + 1 === $token->next->line && $token->line !== $token->prev->line) {
            $value = $token->value;
            $comments[] = $value;
            $token = $token->prev;
        }
        return \implode("\n", \array_reverse($comments));
    }
    private function makeInputValues($values)
    {
        return \Builderius\GraphQL\Utils\Utils::keyValMap($values, static function ($value) {
            return $value->name->value;
        }, function ($value) : array {
            // Note: While this could make assertions to get the correctly typed
            // value, that would throw immediately while type system validation
            // with validateSchema() will produce more actionable results.
            $type = $this->buildWrappedType($value->type);
            $config = ['name' => $value->name->value, 'type' => $type, 'description' => $this->getDescription($value), 'astNode' => $value];
            if (isset($value->defaultValue)) {
                $config['defaultValue'] = \Builderius\GraphQL\Utils\AST::valueFromAST($value->defaultValue, $type);
            }
            return $config;
        });
    }
    /**
     * @return Type|InputType
     *
     * @throws Error
     */
    private function buildWrappedType(\Builderius\GraphQL\Language\AST\TypeNode $typeNode)
    {
        if ($typeNode instanceof \Builderius\GraphQL\Language\AST\ListTypeNode) {
            return \Builderius\GraphQL\Type\Definition\Type::listOf($this->buildWrappedType($typeNode->type));
        }
        if ($typeNode instanceof \Builderius\GraphQL\Language\AST\NonNullTypeNode) {
            return \Builderius\GraphQL\Type\Definition\Type::nonNull($this->buildWrappedType($typeNode->type));
        }
        return $this->buildType($typeNode);
    }
    /**
     * @param string|NamedTypeNode $ref
     *
     * @return Type
     *
     * @throws Error
     */
    public function buildType($ref)
    {
        if (\is_string($ref)) {
            return $this->internalBuildType($ref);
        }
        return $this->internalBuildType($ref->name->value, $ref);
    }
    /**
     * @param string             $typeName
     * @param NamedTypeNode|null $typeNode
     *
     * @return Type
     *
     * @throws Error
     */
    private function internalBuildType($typeName, $typeNode = null)
    {
        if (!isset($this->cache[$typeName])) {
            if (isset($this->typeDefinitionsMap[$typeName])) {
                $type = $this->makeSchemaDef($this->typeDefinitionsMap[$typeName]);
                if ($this->typeConfigDecorator) {
                    $fn = $this->typeConfigDecorator;
                    try {
                        $config = $fn($type->config, $this->typeDefinitionsMap[$typeName], $this->typeDefinitionsMap);
                    } catch (\Throwable $e) {
                        throw new \Builderius\GraphQL\Error\Error(\sprintf('Type config decorator passed to %s threw an error ', static::class) . \sprintf('when building %s type: %s', $typeName, $e->getMessage()), null, null, [], null, $e);
                    }
                    if (!\is_array($config) || isset($config[0])) {
                        throw new \Builderius\GraphQL\Error\Error(\sprintf('Type config decorator passed to %s is expected to return an array, but got %s', static::class, \Builderius\GraphQL\Utils\Utils::getVariableType($config)));
                    }
                    $type = $this->makeSchemaDefFromConfig($this->typeDefinitionsMap[$typeName], $config);
                }
                $this->cache[$typeName] = $type;
            } else {
                $fn = $this->resolveType;
                $this->cache[$typeName] = $fn($typeName, $typeNode);
            }
        }
        return $this->cache[$typeName];
    }
    /**
     * @param ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode|EnumTypeDefinitionNode|ScalarTypeDefinitionNode|InputObjectTypeDefinitionNode|UnionTypeDefinitionNode $def
     *
     * @return CustomScalarType|EnumType|InputObjectType|InterfaceType|ObjectType|UnionType
     *
     * @throws Error
     */
    private function makeSchemaDef(\Builderius\GraphQL\Language\AST\Node $def)
    {
        switch (\true) {
            case $def instanceof \Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode:
                return $this->makeTypeDef($def);
            case $def instanceof \Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode:
                return $this->makeInterfaceDef($def);
            case $def instanceof \Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode:
                return $this->makeEnumDef($def);
            case $def instanceof \Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode:
                return $this->makeUnionDef($def);
            case $def instanceof \Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode:
                return $this->makeScalarDef($def);
            case $def instanceof \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode:
                return $this->makeInputObjectDef($def);
            default:
                throw new \Builderius\GraphQL\Error\Error(\sprintf('Type kind of %s not supported.', $def->kind));
        }
    }
    private function makeTypeDef(\Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode $def)
    {
        $typeName = $def->name->value;
        return new \Builderius\GraphQL\Type\Definition\ObjectType(['name' => $typeName, 'description' => $this->getDescription($def), 'fields' => function () use($def) {
            return $this->makeFieldDefMap($def);
        }, 'interfaces' => function () use($def) {
            return $this->makeImplementedInterfaces($def);
        }, 'astNode' => $def]);
    }
    private function makeFieldDefMap($def)
    {
        return $def->fields ? \Builderius\GraphQL\Utils\Utils::keyValMap($def->fields, static function ($field) {
            return $field->name->value;
        }, function ($field) {
            return $this->buildField($field);
        }) : [];
    }
    public function buildField(\Builderius\GraphQL\Language\AST\FieldDefinitionNode $field)
    {
        return [
            // Note: While this could make assertions to get the correctly typed
            // value, that would throw immediately while type system validation
            // with validateSchema() will produce more actionable results.
            'type' => $this->buildWrappedType($field->type),
            'description' => $this->getDescription($field),
            'args' => isset($field->arguments) ? $this->makeInputValues($field->arguments) : null,
            'deprecationReason' => $this->getDeprecationReason($field),
            'astNode' => $field,
        ];
    }
    /**
     * Given a collection of directives, returns the string value for the
     * deprecation reason.
     *
     * @param EnumValueDefinitionNode | FieldDefinitionNode $node
     *
     * @return string
     */
    private function getDeprecationReason($node)
    {
        $deprecated = \Builderius\GraphQL\Executor\Values::getDirectiveValues(\Builderius\GraphQL\Type\Definition\Directive::deprecatedDirective(), $node);
        return $deprecated['reason'] ?? null;
    }
    private function makeImplementedInterfaces(\Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode $def)
    {
        if ($def->interfaces !== null) {
            // Note: While this could make early assertions to get the correctly
            // typed values, that would throw immediately while type system
            // validation with validateSchema() will produce more actionable results.
            return \Builderius\GraphQL\Utils\Utils::map($def->interfaces, function ($iface) : Type {
                return $this->buildType($iface);
            });
        }
        return null;
    }
    private function makeInterfaceDef(\Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode $def)
    {
        $typeName = $def->name->value;
        return new \Builderius\GraphQL\Type\Definition\InterfaceType(['name' => $typeName, 'description' => $this->getDescription($def), 'fields' => function () use($def) {
            return $this->makeFieldDefMap($def);
        }, 'astNode' => $def]);
    }
    private function makeEnumDef(\Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode $def)
    {
        return new \Builderius\GraphQL\Type\Definition\EnumType(['name' => $def->name->value, 'description' => $this->getDescription($def), 'values' => $def->values ? \Builderius\GraphQL\Utils\Utils::keyValMap($def->values, static function ($enumValue) {
            return $enumValue->name->value;
        }, function ($enumValue) : array {
            return ['description' => $this->getDescription($enumValue), 'deprecationReason' => $this->getDeprecationReason($enumValue), 'astNode' => $enumValue];
        }) : [], 'astNode' => $def]);
    }
    private function makeUnionDef(\Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode $def)
    {
        return new \Builderius\GraphQL\Type\Definition\UnionType([
            'name' => $def->name->value,
            'description' => $this->getDescription($def),
            // Note: While this could make assertions to get the correctly typed
            // values below, that would throw immediately while type system
            // validation with validateSchema() will produce more actionable results.
            'types' => isset($def->types) ? function () use($def) {
                return \Builderius\GraphQL\Utils\Utils::map($def->types, function ($typeNode) : Type {
                    return $this->buildType($typeNode);
                });
            } : [],
            'astNode' => $def,
        ]);
    }
    private function makeScalarDef(\Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode $def)
    {
        return new \Builderius\GraphQL\Type\Definition\CustomScalarType(['name' => $def->name->value, 'description' => $this->getDescription($def), 'astNode' => $def, 'serialize' => static function ($value) {
            return $value;
        }]);
    }
    private function makeInputObjectDef(\Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode $def)
    {
        return new \Builderius\GraphQL\Type\Definition\InputObjectType(['name' => $def->name->value, 'description' => $this->getDescription($def), 'fields' => function () use($def) {
            return $def->fields !== null ? $this->makeInputValues($def->fields) : [];
        }, 'astNode' => $def]);
    }
    /**
     * @param mixed[] $config
     *
     * @return CustomScalarType|EnumType|InputObjectType|InterfaceType|ObjectType|UnionType
     *
     * @throws Error
     */
    private function makeSchemaDefFromConfig(\Builderius\GraphQL\Language\AST\Node $def, array $config)
    {
        switch (\true) {
            case $def instanceof \Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode:
                return new \Builderius\GraphQL\Type\Definition\ObjectType($config);
            case $def instanceof \Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode:
                return new \Builderius\GraphQL\Type\Definition\InterfaceType($config);
            case $def instanceof \Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode:
                return new \Builderius\GraphQL\Type\Definition\EnumType($config);
            case $def instanceof \Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode:
                return new \Builderius\GraphQL\Type\Definition\UnionType($config);
            case $def instanceof \Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode:
                return new \Builderius\GraphQL\Type\Definition\CustomScalarType($config);
            case $def instanceof \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode:
                return new \Builderius\GraphQL\Type\Definition\InputObjectType($config);
            default:
                throw new \Builderius\GraphQL\Error\Error(\sprintf('Type kind of %s not supported.', $def->kind));
        }
    }
    /**
     * @return mixed[]
     */
    public function buildInputField(\Builderius\GraphQL\Language\AST\InputValueDefinitionNode $value) : array
    {
        $type = $this->buildWrappedType($value->type);
        $config = ['name' => $value->name->value, 'type' => $type, 'description' => $this->getDescription($value), 'astNode' => $value];
        if ($value->defaultValue !== null) {
            $config['defaultValue'] = $value->defaultValue;
        }
        return $config;
    }
    /**
     * @return mixed[]
     */
    public function buildEnumValue(\Builderius\GraphQL\Language\AST\EnumValueDefinitionNode $value) : array
    {
        return ['description' => $this->getDescription($value), 'deprecationReason' => $this->getDeprecationReason($value), 'astNode' => $value];
    }
}
