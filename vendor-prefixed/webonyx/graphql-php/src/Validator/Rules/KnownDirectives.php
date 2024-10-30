<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumTypeExtensionNode;
use Builderius\GraphQL\Language\AST\EnumValueDefinitionNode;
use Builderius\GraphQL\Language\AST\FieldDefinitionNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NodeList;
use Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode;
use Builderius\GraphQL\Language\AST\SchemaDefinitionNode;
use Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode;
use Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\UnionTypeExtensionNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Language\DirectiveLocation;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ASTValidationContext;
use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_map;
use function count;
use function get_class;
use function in_array;
use function sprintf;
class KnownDirectives extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
    public function getSDLVisitor(\Builderius\GraphQL\Validator\SDLValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
    public function getASTVisitor(\Builderius\GraphQL\Validator\ASTValidationContext $context)
    {
        $locationsMap = [];
        $schema = $context->getSchema();
        $definedDirectives = $schema ? $schema->getDirectives() : \Builderius\GraphQL\Type\Definition\Directive::getInternalDirectives();
        foreach ($definedDirectives as $directive) {
            $locationsMap[$directive->name] = $directive->locations;
        }
        $astDefinition = $context->getDocument()->definitions;
        foreach ($astDefinition as $def) {
            if (!$def instanceof \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode) {
                continue;
            }
            $locationsMap[$def->name->value] = \Builderius\GraphQL\Utils\Utils::map($def->locations, static function ($name) : string {
                return $name->value;
            });
        }
        return [\Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE => function (\Builderius\GraphQL\Language\AST\DirectiveNode $node, $key, $parent, $path, $ancestors) use($context, $locationsMap) : void {
            $name = $node->name->value;
            $locations = $locationsMap[$name] ?? null;
            if (!$locations) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::unknownDirectiveMessage($name), [$node]));
                return;
            }
            $candidateLocation = $this->getDirectiveLocationForASTPath($ancestors);
            if (!$candidateLocation || \in_array($candidateLocation, $locations, \true)) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::misplacedDirectiveMessage($name, $candidateLocation), [$node]));
        }];
    }
    public static function unknownDirectiveMessage($directiveName)
    {
        return \sprintf('Unknown directive "%s".', $directiveName);
    }
    /**
     * @param Node[]|NodeList[] $ancestors The type is actually (Node|NodeList)[] but this PSR-5 syntax is so far not supported by most of the tools
     *
     * @return string
     */
    private function getDirectiveLocationForASTPath(array $ancestors)
    {
        $appliedTo = $ancestors[\count($ancestors) - 1];
        switch (\true) {
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\OperationDefinitionNode:
                switch ($appliedTo->operation) {
                    case 'query':
                        return \Builderius\GraphQL\Language\DirectiveLocation::QUERY;
                    case 'mutation':
                        return \Builderius\GraphQL\Language\DirectiveLocation::MUTATION;
                    case 'subscription':
                        return \Builderius\GraphQL\Language\DirectiveLocation::SUBSCRIPTION;
                }
                break;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\FieldNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::FIELD;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\FragmentSpreadNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::FRAGMENT_SPREAD;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::INLINE_FRAGMENT;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\FragmentDefinitionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::FRAGMENT_DEFINITION;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\VariableDefinitionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::VARIABLE_DEFINITION;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\SchemaDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::SCHEMA;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::SCALAR;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::OBJECT;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\FieldDefinitionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::FIELD_DEFINITION;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::IFACE;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\UnionTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::UNION;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\EnumTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::ENUM;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\EnumValueDefinitionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::ENUM_VALUE;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode:
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode:
                return \Builderius\GraphQL\Language\DirectiveLocation::INPUT_OBJECT;
            case $appliedTo instanceof \Builderius\GraphQL\Language\AST\InputValueDefinitionNode:
                $parentNode = $ancestors[\count($ancestors) - 3];
                return $parentNode instanceof \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode ? \Builderius\GraphQL\Language\DirectiveLocation::INPUT_FIELD_DEFINITION : \Builderius\GraphQL\Language\DirectiveLocation::ARGUMENT_DEFINITION;
        }
        throw new \Exception('Unknown directive location: ' . \get_class($appliedTo));
    }
    public static function misplacedDirectiveMessage($directiveName, $location)
    {
        return \sprintf('Directive "%s" may not be used on "%s".', $directiveName, $location);
    }
}
