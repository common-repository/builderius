<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class FieldDefinitionNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::FIELD_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<InputValueDefinitionNode> */
    public $arguments;
    /** @var NamedTypeNode|ListTypeNode|NonNullTypeNode */
    public $type;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var StringValueNode|null */
    public $description;
}
