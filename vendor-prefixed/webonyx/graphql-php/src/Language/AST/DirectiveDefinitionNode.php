<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class DirectiveDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var StringValueNode|null */
    public $description;
    /** @var NodeList<InputValueDefinitionNode> */
    public $arguments;
    /** @var bool */
    public $repeatable;
    /** @var NodeList<NameNode> */
    public $locations;
}
