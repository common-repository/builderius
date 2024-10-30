<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class UniqueOperationNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownOperationNames;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $this->knownOperationNames = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $node) use($context) : VisitorOperation {
            $operationName = $node->name;
            if ($operationName !== null) {
                if (!isset($this->knownOperationNames[$operationName->value])) {
                    $this->knownOperationNames[$operationName->value] = $operationName;
                } else {
                    $context->reportError(new \Builderius\GraphQL\Error\Error(self::duplicateOperationNameMessage($operationName->value), [$this->knownOperationNames[$operationName->value], $operationName]));
                }
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => static function () : VisitorOperation {
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    public static function duplicateOperationNameMessage($operationName)
    {
        return \sprintf('There can be only one operation named "%s".', $operationName);
    }
}
