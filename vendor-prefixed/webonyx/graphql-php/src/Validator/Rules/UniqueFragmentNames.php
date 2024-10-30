<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class UniqueFragmentNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownFragmentNames;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $this->knownFragmentNames = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => static function () : VisitorOperation {
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => function (\Builderius\GraphQL\Language\AST\FragmentDefinitionNode $node) use($context) : VisitorOperation {
            $fragmentName = $node->name->value;
            if (!isset($this->knownFragmentNames[$fragmentName])) {
                $this->knownFragmentNames[$fragmentName] = $node->name;
            } else {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::duplicateFragmentNameMessage($fragmentName), [$this->knownFragmentNames[$fragmentName], $node->name]));
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    public static function duplicateFragmentNameMessage($fragName)
    {
        return \sprintf('There can be only one fragment named "%s".', $fragName);
    }
}
