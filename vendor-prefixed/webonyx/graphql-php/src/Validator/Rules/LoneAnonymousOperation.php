<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ValidationContext;
use function count;
/**
 * Lone anonymous operation
 *
 * A GraphQL document is only valid if when it contains an anonymous operation
 * (the query short-hand) that it contains only that one operation definition.
 */
class LoneAnonymousOperation extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $operationCount = 0;
        return [\Builderius\GraphQL\Language\AST\NodeKind::DOCUMENT => static function (\Builderius\GraphQL\Language\AST\DocumentNode $node) use(&$operationCount) : void {
            $tmp = \Builderius\GraphQL\Utils\Utils::filter($node->definitions, static function (\Builderius\GraphQL\Language\AST\Node $definition) : bool {
                return $definition instanceof \Builderius\GraphQL\Language\AST\OperationDefinitionNode;
            });
            $operationCount = \count($tmp);
        }, \Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => static function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $node) use(&$operationCount, $context) : void {
            if ($node->name !== null || $operationCount <= 1) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::anonOperationNotAloneMessage(), [$node]));
        }];
    }
    public static function anonOperationNotAloneMessage()
    {
        return 'This anonymous operation must be the only defined operation.';
    }
}
