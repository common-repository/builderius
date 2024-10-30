<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class OperationDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\ExecutableDefinitionNode, \Builderius\GraphQL\Language\AST\HasSelectionSet
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION;
    /** @var NameNode|null */
    public $name;
    /** @var string (oneOf 'query', 'mutation', 'subscription')) */
    public $operation;
    /** @var NodeList<VariableDefinitionNode> */
    public $variableDefinitions;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var SelectionSetNode */
    public $selectionSet;
}
