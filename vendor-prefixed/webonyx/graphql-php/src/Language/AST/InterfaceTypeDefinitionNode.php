<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class InterfaceTypeDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode>|null */
    public $directives;
    /** @var NodeList<FieldDefinitionNode>|null */
    public $fields;
    /** @var StringValueNode|null */
    public $description;
}
