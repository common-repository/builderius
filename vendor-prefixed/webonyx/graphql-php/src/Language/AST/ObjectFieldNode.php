<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class ObjectFieldNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_FIELD;
    /** @var NameNode */
    public $name;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode */
    public $value;
}
