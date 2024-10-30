<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class SelectionSetNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::SELECTION_SET;
    /** @var NodeList<SelectionNode&Node> */
    public $selections;
}
