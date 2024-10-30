<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class SchemaDefinitionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::SCHEMA_DEFINITION;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<OperationTypeDefinitionNode> */
    public $operationTypes;
}
