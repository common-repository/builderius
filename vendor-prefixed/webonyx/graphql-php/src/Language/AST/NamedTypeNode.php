<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class NamedTypeNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::NAMED_TYPE;
    /** @var NameNode */
    public $name;
}
