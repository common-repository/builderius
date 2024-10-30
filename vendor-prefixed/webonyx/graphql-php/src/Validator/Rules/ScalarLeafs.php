<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class ScalarLeafs extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::FIELD => static function (\Builderius\GraphQL\Language\AST\FieldNode $node) use($context) : void {
            $type = $context->getType();
            if (!$type) {
                return;
            }
            if (\Builderius\GraphQL\Type\Definition\Type::isLeafType(\Builderius\GraphQL\Type\Definition\Type::getNamedType($type))) {
                if ($node->selectionSet) {
                    $context->reportError(new \Builderius\GraphQL\Error\Error(self::noSubselectionAllowedMessage($node->name->value, $type), [$node->selectionSet]));
                }
            } elseif (!$node->selectionSet) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::requiredSubselectionMessage($node->name->value, $type), [$node]));
            }
        }];
    }
    public static function noSubselectionAllowedMessage($field, $type)
    {
        return \sprintf('Field "%s" of type "%s" must not have a sub selection.', $field, $type);
    }
    public static function requiredSubselectionMessage($field, $type)
    {
        return \sprintf('Field "%s" of type "%s" must have a sub selection.', $field, $type);
    }
}
