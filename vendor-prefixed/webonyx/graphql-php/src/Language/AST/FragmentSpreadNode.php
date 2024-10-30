<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class FragmentSpreadNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\SelectionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_SPREAD;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
}
