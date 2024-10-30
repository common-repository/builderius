<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class VariableDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\DefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION;
    /** @var VariableNode */
    public $variable;
    /** @var NamedTypeNode|ListTypeNode|NonNullTypeNode */
    public $type;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null */
    public $defaultValue;
    /** @var NodeList<DirectiveNode> */
    public $directives;
}
