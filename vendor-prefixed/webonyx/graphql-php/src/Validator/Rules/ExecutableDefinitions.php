<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\ExecutableDefinitionNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
/**
 * Executable definitions
 *
 * A GraphQL document is only valid for execution if all definitions are either
 * operation or fragment definitions.
 */
class ExecutableDefinitions extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::DOCUMENT => static function (\Builderius\GraphQL\Language\AST\DocumentNode $node) use($context) : VisitorOperation {
            /** @var ExecutableDefinitionNode|TypeSystemDefinitionNode $definition */
            foreach ($node->definitions as $definition) {
                if ($definition instanceof \Builderius\GraphQL\Language\AST\ExecutableDefinitionNode) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::nonExecutableDefinitionMessage($definition->name->value), [$definition->name]));
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    public static function nonExecutableDefinitionMessage($defName)
    {
        return \sprintf('The "%s" definition is not executable.', $defName);
    }
}
