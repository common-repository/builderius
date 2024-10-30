<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class FragmentDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\ExecutableDefinitionNode, \Builderius\GraphQL\Language\AST\HasSelectionSet
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION;
    /** @var NameNode */
    public $name;
    /**
     * Note: fragment variable definitions are experimental and may be changed
     * or removed in the future.
     *
     * @var NodeList<VariableDefinitionNode>
     */
    public $variableDefinitions;
    /** @var NamedTypeNode */
    public $typeCondition;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var SelectionSetNode */
    public $selectionSet;
}
