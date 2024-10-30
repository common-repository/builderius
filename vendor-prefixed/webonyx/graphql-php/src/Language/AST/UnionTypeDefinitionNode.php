<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class UnionTypeDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::UNION_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<NamedTypeNode>|null */
    public $types;
    /** @var StringValueNode|null */
    public $description;
}
