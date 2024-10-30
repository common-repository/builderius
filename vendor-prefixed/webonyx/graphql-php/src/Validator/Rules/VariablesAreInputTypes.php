<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Language\Printer;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Utils\TypeInfo;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class VariablesAreInputTypes extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => static function (\Builderius\GraphQL\Language\AST\VariableDefinitionNode $node) use($context) : void {
            $type = \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($context->getSchema(), $node->type);
            // If the variable type is not an input type, return an error.
            if (!$type || \Builderius\GraphQL\Type\Definition\Type::isInputType($type)) {
                return;
            }
            $variableName = $node->variable->name->value;
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::nonInputTypeOnVarMessage($variableName, \Builderius\GraphQL\Language\Printer::doPrint($node->type)), [$node->type]));
        }];
    }
    public static function nonInputTypeOnVarMessage($variableName, $typeName)
    {
        return \sprintf('Variable "$%s" cannot be non-input type "%s".', $variableName, $typeName);
    }
}
