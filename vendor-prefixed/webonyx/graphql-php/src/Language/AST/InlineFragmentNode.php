<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class InlineFragmentNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\SelectionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::INLINE_FRAGMENT;
    /** @var NamedTypeNode */
    public $typeCondition;
    /** @var NodeList<DirectiveNode>|null */
    public $directives;
    /** @var SelectionSetNode */
    public $selectionSet;
}
