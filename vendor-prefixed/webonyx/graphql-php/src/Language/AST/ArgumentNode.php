<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class ArgumentNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::ARGUMENT;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode */
    public $value;
    /** @var NameNode */
    public $name;
}
