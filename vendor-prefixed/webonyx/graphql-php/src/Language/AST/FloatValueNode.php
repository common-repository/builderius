<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class FloatValueNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::FLOAT;
    /** @var string */
    public $value;
}
