<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Printer;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Utils\TypeInfo;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class FragmentsOnCompositeTypes extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::INLINE_FRAGMENT => static function (\Builderius\GraphQL\Language\AST\InlineFragmentNode $node) use($context) : void {
            if (!$node->typeCondition) {
                return;
            }
            $type = \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($context->getSchema(), $node->typeCondition);
            if (!$type || \Builderius\GraphQL\Type\Definition\Type::isCompositeType($type)) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(static::inlineFragmentOnNonCompositeErrorMessage($type), [$node->typeCondition]));
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => static function (\Builderius\GraphQL\Language\AST\FragmentDefinitionNode $node) use($context) : void {
            $type = \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($context->getSchema(), $node->typeCondition);
            if (!$type || \Builderius\GraphQL\Type\Definition\Type::isCompositeType($type)) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(static::fragmentOnNonCompositeErrorMessage($node->name->value, \Builderius\GraphQL\Language\Printer::doPrint($node->typeCondition)), [$node->typeCondition]));
        }];
    }
    public static function inlineFragmentOnNonCompositeErrorMessage($type)
    {
        return \sprintf('Fragment cannot condition on non composite type "%s".', $type);
    }
    public static function fragmentOnNonCompositeErrorMessage($fragName, $type)
    {
        return \sprintf('Fragment "%s" cannot condition on non composite type "%s".', $fragName, $type);
    }
}
