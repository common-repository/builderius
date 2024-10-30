<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class OperationTypeDefinitionNode extends \Builderius\GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::OPERATION_TYPE_DEFINITION;
    /**
     * One of 'query' | 'mutation' | 'subscription'
     *
     * @var string
     */
    public $operation;
    /** @var NamedTypeNode */
    public $type;
}
