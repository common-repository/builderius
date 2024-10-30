<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class UniqueVariableNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownVariableNames;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $this->knownVariableNames = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => function () : void {
            $this->knownVariableNames = [];
        }, \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => function (\Builderius\GraphQL\Language\AST\VariableDefinitionNode $node) use($context) : void {
            $variableName = $node->variable->name->value;
            if (!isset($this->knownVariableNames[$variableName])) {
                $this->knownVariableNames[$variableName] = $node->variable->name;
            } else {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::duplicateVariableMessage($variableName), [$this->knownVariableNames[$variableName], $node->variable->name]));
            }
        }];
    }
    public static function duplicateVariableMessage($variableName)
    {
        return \sprintf('There can be only one variable named "%s".', $variableName);
    }
}
