<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class DirectiveNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE;
    /** @var NameNode */
    public $name;
    /** @var NodeList<ArgumentNode> */
    public $arguments;
}
