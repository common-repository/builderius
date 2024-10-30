<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class NonNullTypeNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::NON_NULL_TYPE;
    /** @var NamedTypeNode|ListTypeNode */
    public $type;
}
