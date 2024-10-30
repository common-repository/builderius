<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class ObjectTypeExtensionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<NamedTypeNode> */
    public $interfaces;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<FieldDefinitionNode> */
    public $fields;
}
