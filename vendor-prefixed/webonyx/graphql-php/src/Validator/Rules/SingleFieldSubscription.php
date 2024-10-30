<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NodeList;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_splice;
use function count;
use function sprintf;
class SingleFieldSubscription extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /**
     * @return array<string, callable>
     */
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context) : array
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => static function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $node) use($context) : VisitorOperation {
            if ($node->operation === 'subscription') {
                $selections = $node->selectionSet->selections;
                if (\count($selections) !== 1) {
                    if ($selections instanceof \Builderius\GraphQL\Language\AST\NodeList) {
                        $offendingSelections = $selections->splice(1, \count($selections));
                    } else {
                        $offendingSelections = \array_splice($selections, 1);
                    }
                    $context->reportError(new \Builderius\GraphQL\Error\Error(self::multipleFieldsInOperation($node->name->value ?? null), $offendingSelections));
                }
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    public static function multipleFieldsInOperation(?string $operationName) : string
    {
        if ($operationName === null) {
            return \sprintf('Anonymous Subscription must select only one top level field.');
        }
        return \sprintf('Subscription "%s" must select only one top level field.', $operationName);
    }
}
