<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class NoUnusedFragments extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var OperationDefinitionNode[] */
    public $operationDefs;
    /** @var FragmentDefinitionNode[] */
    public $fragmentDefs;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $this->operationDefs = [];
        $this->fragmentDefs = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => function ($node) : VisitorOperation {
            $this->operationDefs[] = $node;
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => function (\Builderius\GraphQL\Language\AST\FragmentDefinitionNode $def) : VisitorOperation {
            $this->fragmentDefs[] = $def;
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }, \Builderius\GraphQL\Language\AST\NodeKind::DOCUMENT => ['leave' => function () use($context) : void {
            $fragmentNameUsed = [];
            foreach ($this->operationDefs as $operation) {
                foreach ($context->getRecursivelyReferencedFragments($operation) as $fragment) {
                    $fragmentNameUsed[$fragment->name->value] = \true;
                }
            }
            foreach ($this->fragmentDefs as $fragmentDef) {
                $fragName = $fragmentDef->name->value;
                if ($fragmentNameUsed[$fragName] ?? \false) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::unusedFragMessage($fragName), [$fragmentDef]));
            }
        }]];
    }
    public static function unusedFragMessage($fragName)
    {
        return \sprintf('Fragment "%s" is never used.', $fragName);
    }
}
