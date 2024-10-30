<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

/**
 * export type ExecutableDefinitionNode =
 *   | OperationDefinitionNode
 *   | FragmentDefinitionNode;
 */
interface ExecutableDefinitionNode extends \Builderius\GraphQL\Language\AST\DefinitionNode
{
}
