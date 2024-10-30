<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class ObjectTypeDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<NamedTypeNode> */
    public $interfaces;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<FieldDefinitionNode>|null */
    public $fields;
    /** @var StringValueNode|null */
    public $description;
}
