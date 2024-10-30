<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class BooleanValueNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::BOOLEAN;
    /** @var bool */
    public $value;
}
