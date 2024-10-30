<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NamedTypeNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_keys;
use function count;
use function sprintf;
/**
 * Known type names
 *
 * A GraphQL document is only valid if referenced types (specifically
 * variable definitions and fragment conditions) are defined by the type schema.
 */
class KnownTypeNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $skip = static function () : VisitorOperation {
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        };
        return [
            // TODO: when validating IDL, re-enable these. Experimental version does not
            // add unreferenced types, resulting in false-positive errors. Squelched
            // errors for now.
            \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_TYPE_DEFINITION => $skip,
            \Builderius\GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_DEFINITION => $skip,
            \Builderius\GraphQL\Language\AST\NodeKind::UNION_TYPE_DEFINITION => $skip,
            \Builderius\GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_DEFINITION => $skip,
            \Builderius\GraphQL\Language\AST\NodeKind::NAMED_TYPE => static function (\Builderius\GraphQL\Language\AST\NamedTypeNode $node) use($context) : void {
                $schema = $context->getSchema();
                $typeName = $node->name->value;
                $type = $schema->getType($typeName);
                if ($type !== null) {
                    return;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::unknownTypeMessage($typeName, \Builderius\GraphQL\Utils\Utils::suggestionList($typeName, \array_keys($schema->getTypeMap()))), [$node]));
            },
        ];
    }
    /**
     * @param string   $type
     * @param string[] $suggestedTypes
     */
    public static function unknownTypeMessage($type, array $suggestedTypes)
    {
        $message = \sprintf('Unknown type "%s".', $type);
        if (\count($suggestedTypes) > 0) {
            $suggestions = \Builderius\GraphQL\Utils\Utils::quotedOrList($suggestedTypes);
            $message .= \sprintf(' Did you mean %s?', $suggestions);
        }
        return $message;
    }
}
