<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
/**
 * A GraphQL operation is only valid if all variables encountered, both directly
 * and via fragment spreads, are defined by that operation.
 */
class NoUndefinedVariables extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $variableNameDefined = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => ['enter' => static function () use(&$variableNameDefined) : void {
            $variableNameDefined = [];
        }, 'leave' => static function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operation) use(&$variableNameDefined, $context) : void {
            $usages = $context->getRecursiveVariableUsages($operation);
            foreach ($usages as $usage) {
                $node = $usage['node'];
                $varName = $node->name->value;
                if ($variableNameDefined[$varName] ?? \false) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::undefinedVarMessage($varName, $operation->name !== null ? $operation->name->value : null), [$node, $operation]));
            }
        }], \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => static function (\Builderius\GraphQL\Language\AST\VariableDefinitionNode $def) use(&$variableNameDefined) : void {
            $variableNameDefined[$def->variable->name->value] = \true;
        }];
    }
    public static function undefinedVarMessage($varName, $opName = null)
    {
        return $opName ? \sprintf('Variable "$%s" is not defined by operation "%s".', $varName, $opName) : \sprintf('Variable "$%s" is not defined.', $varName);
    }
}
