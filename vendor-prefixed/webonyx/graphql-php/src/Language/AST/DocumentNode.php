<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class DocumentNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::DOCUMENT;
    /** @var NodeList<DefinitionNode&Node> */
    public $definitions;
}
