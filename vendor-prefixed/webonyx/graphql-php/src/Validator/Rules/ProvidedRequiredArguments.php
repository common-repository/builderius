<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class ProvidedRequiredArguments extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $providedRequiredArgumentsOnDirectives = new \Builderius\GraphQL\Validator\Rules\ProvidedRequiredArgumentsOnDirectives();
        return $providedRequiredArgumentsOnDirectives->getVisitor($context) + [\Builderius\GraphQL\Language\AST\NodeKind::FIELD => ['leave' => static function (\Builderius\GraphQL\Language\AST\FieldNode $fieldNode) use($context) : ?VisitorOperation {
            $fieldDef = $context->getFieldDef();
            if (!$fieldDef) {
                return \Builderius\GraphQL\Language\Visitor::skipNode();
            }
            $argNodes = $fieldNode->arguments ?? [];
            $argNodeMap = [];
            foreach ($argNodes as $argNode) {
                $argNodeMap[$argNode->name->value] = $argNode;
            }
            foreach ($fieldDef->args as $argDef) {
                $argNode = $argNodeMap[$argDef->name] ?? null;
                if ($argNode || !$argDef->isRequired()) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::missingFieldArgMessage($fieldNode->name->value, $argDef->name, $argDef->getType()), [$fieldNode]));
            }
            return null;
        }]];
    }
    public static function missingFieldArgMessage($fieldName, $argName, $type)
    {
        return \sprintf('Field "%s" argument "%s" of type "%s" is required but not provided.', $fieldName, $argName, $type);
    }
}
