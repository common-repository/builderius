<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class EnumValueDefinitionNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::ENUM_VALUE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var StringValueNode|null */
    public $description;
}
