<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

/**
 * export type TypeExtensionNode =
 * | ScalarTypeExtensionNode
 * | ObjectTypeExtensionNode
 * | InterfaceTypeExtensionNode
 * | UnionTypeExtensionNode
 * | EnumTypeExtensionNode
 * | InputObjectTypeExtensionNode;
 */
interface TypeExtensionNode extends \Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode
{
}
