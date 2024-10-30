<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

/**
 * export type TypeDefinitionNode = ScalarTypeDefinitionNode
 * | ObjectTypeDefinitionNode
 * | InterfaceTypeDefinitionNode
 * | UnionTypeDefinitionNode
 * | EnumTypeDefinitionNode
 * | InputObjectTypeDefinitionNode
 */
interface TypeDefinitionNode extends \Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode
{
}
