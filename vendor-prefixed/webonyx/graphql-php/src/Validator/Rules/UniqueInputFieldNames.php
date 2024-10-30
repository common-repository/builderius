<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\ObjectFieldNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ASTValidationContext;
use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_pop;
use function sprintf;
class UniqueInputFieldNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var array<string, NameNode> */
    public $knownNames;
    /** @var array<array<string, NameNode>> */
    public $knownNameStack;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
    public function getSDLVisitor(\Builderius\GraphQL\Validator\SDLValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
    public function getASTVisitor(\Builderius\GraphQL\Validator\ASTValidationContext $context)
    {
        $this->knownNames = [];
        $this->knownNameStack = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OBJECT => ['enter' => function () : void {
            $this->knownNameStack[] = $this->knownNames;
            $this->knownNames = [];
        }, 'leave' => function () : void {
            $this->knownNames = \array_pop($this->knownNameStack);
        }], \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_FIELD => function (\Builderius\GraphQL\Language\AST\ObjectFieldNode $node) use($context) : VisitorOperation {
            $fieldName = $node->name->value;
            if (isset($this->knownNames[$fieldName])) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::duplicateInputFieldMessage($fieldName), [$this->knownNames[$fieldName], $node->name]));
            } else {
                $this->knownNames[$fieldName] = $node->name;
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    public static function duplicateInputFieldMessage($fieldName)
    {
        return \sprintf('There can be only one input field named "%s".', $fieldName);
    }
}
