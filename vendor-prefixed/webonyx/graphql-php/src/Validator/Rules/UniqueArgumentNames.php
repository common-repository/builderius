<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ASTValidationContext;
use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class UniqueArgumentNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownArgNames;
    public function getSDLVisitor(\Builderius\GraphQL\Validator\SDLValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
    public function getASTVisitor(\Builderius\GraphQL\Validator\ASTValidationContext $context)
    {
        $this->knownArgNames = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::FIELD => function () : void {
            $this->knownArgNames = [];
        }, \Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE => function () : void {
            $this->knownArgNames = [];
        }, \Builderius\GraphQL\Language\AST\NodeKind::ARGUMENT => function (\Builderius\GraphQL\Language\AST\ArgumentNode $node) use($context) : VisitorOperation {
            $argName = $node->name->value;
            if ($this->knownArgNames[$argName] ?? \false) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::duplicateArgMessage($argName), [$this->knownArgNames[$argName], $node->name]));
            } else {
                $this->knownArgNames[$argName] = $node->name;
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    public static function duplicateArgMessage($argName)
    {
        return \sprintf('There can be only one argument named "%s".', $argName);
    }
}
