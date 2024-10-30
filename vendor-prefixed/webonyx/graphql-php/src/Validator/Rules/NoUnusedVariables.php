<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class NoUnusedVariables extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var VariableDefinitionNode[] */
    public $variableDefs;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $this->variableDefs = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => ['enter' => function () : void {
            $this->variableDefs = [];
        }, 'leave' => function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operation) use($context) : void {
            $variableNameUsed = [];
            $usages = $context->getRecursiveVariableUsages($operation);
            $opName = $operation->name !== null ? $operation->name->value : null;
            foreach ($usages as $usage) {
                $node = $usage['node'];
                $variableNameUsed[$node->name->value] = \true;
            }
            foreach ($this->variableDefs as $variableDef) {
                $variableName = $variableDef->variable->name->value;
                if ($variableNameUsed[$variableName] ?? \false) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::unusedVariableMessage($variableName, $opName), [$variableDef]));
            }
        }], \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => function ($def) : void {
            $this->variableDefs[] = $def;
        }];
    }
    public static function unusedVariableMessage($varName, $opName = null)
    {
        return $opName ? \sprintf('Variable "$%s" is never used in operation "%s".', $varName, $opName) : \sprintf('Variable "$%s" is never used.', $varName);
    }
}
