<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class StringValueNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::STRING;
    /** @var string */
    public $value;
    /** @var bool|null */
    public $block;
}
