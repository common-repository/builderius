<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language\AST;

class SchemaTypeExtensionNode extends \Builderius\GraphQL\Language\AST\Node implements \Builderius\GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \Builderius\GraphQL\Language\AST\NodeKind::SCHEMA_EXTENSION;
    /** @var NodeList<DirectiveNode>|null */
    public $directives;
    /** @var NodeList<OperationTypeDefinitionNode>|null */
    public $operationTypes;
}
