<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class FieldNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\SelectionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::FIELD;
    /** @var NameNode */
    public $name;
    /** @var NameNode|null */
    public $alias;
    /** @var NodeList<ArgumentNode>|null */
    public $arguments;
    /** @var NodeList<DirectiveNode>|null */
    public $directives;
    /** @var SelectionSetNode|null */
    public $selectionSet;
}
