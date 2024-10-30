<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class EnumTypeDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::ENUM_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<EnumValueDefinitionNode>|null */
    public $values;
    /** @var StringValueNode|null */
    public $description;
}
