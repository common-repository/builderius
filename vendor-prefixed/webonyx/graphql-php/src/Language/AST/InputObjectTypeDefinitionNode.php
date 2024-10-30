<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class InputObjectTypeDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode>|null */
    public $directives;
    /** @var NodeList<InputValueDefinitionNode>|null */
    public $fields;
    /** @var StringValueNode|null */
    public $description;
}
