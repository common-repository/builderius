<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_map;
use function count;
use function sprintf;
/**
 * Known argument names
 *
 * A GraphQL field is only valid if all supplied arguments are defined by
 * that field.
 */
class KnownArgumentNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $knownArgumentNamesOnDirectives = new \Builderius\GraphQL\Validator\Rules\KnownArgumentNamesOnDirectives();
        return $knownArgumentNamesOnDirectives->getVisitor($context) + [\Builderius\GraphQL\Language\AST\NodeKind::ARGUMENT => static function (\Builderius\GraphQL\Language\AST\ArgumentNode $node) use($context) : void {
            $argDef = $context->getArgument();
            if ($argDef !== null) {
                return;
            }
            $fieldDef = $context->getFieldDef();
            $parentType = $context->getParentType();
            if ($fieldDef === null || !$parentType instanceof \Builderius\GraphQL\Type\Definition\Type) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::unknownArgMessage($node->name->value, $fieldDef->name, $parentType->name, \Builderius\GraphQL\Utils\Utils::suggestionList($node->name->value, \array_map(static function ($arg) : string {
                return $arg->name;
            }, $fieldDef->args))), [$node]));
            return;
        }];
    }
    /**
     * @param string[] $suggestedArgs
     */
    public static function unknownArgMessage($argName, $fieldName, $typeName, array $suggestedArgs)
    {
        $message = \sprintf('Unknown argument "%s" on field "%s" of type "%s".', $argName, $fieldName, $typeName);
        if (isset($suggestedArgs[0])) {
            $message .= \sprintf(' Did you mean %s?', \Builderius\GraphQL\Utils\Utils::quotedOrList($suggestedArgs));
        }
        return $message;
    }
}
