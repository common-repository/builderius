<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class InputObjectTypeExtensionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode>|null */
    public $directives;
    /** @var NodeList<InputValueDefinitionNode>|null */
    public $fields;
}