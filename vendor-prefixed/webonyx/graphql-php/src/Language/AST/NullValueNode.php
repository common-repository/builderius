<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class NullValueNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::NULL;
}
