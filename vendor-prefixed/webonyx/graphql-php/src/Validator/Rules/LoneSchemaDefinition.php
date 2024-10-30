<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\SchemaDefinitionNode;
use Builderius\GraphQL\Validator\SDLValidationContext;
/**
 * Lone Schema definition
 *
 * A GraphQL document is only valid if it contains only one schema definition.
 */
class LoneSchemaDefinition extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public static function schemaDefinitionNotAloneMessage()
    {
        return 'Must provide only one schema definition.';
    }
    public static function canNotDefineSchemaWithinExtensionMessage()
    {
        return 'Cannot define a new schema within a schema extension.';
    }
    public function getSDLVisitor(\Builderius\GraphQL\Validator\SDLValidationContext $context)
    {
        $oldSchema = $context->getSchema();
        $alreadyDefined = $oldSchema !== null ? $oldSchema->getAstNode() !== null || $oldSchema->getQueryType() !== null || $oldSchema->getMutationType() !== null || $oldSchema->getSubscriptionType() !== null : \false;
        $schemaDefinitionsCount = 0;
        return [\Builderius\GraphQL\Language\AST\NodeKind::SCHEMA_DEFINITION => static function (\Builderius\GraphQL\Language\AST\SchemaDefinitionNode $node) use($alreadyDefined, $context, &$schemaDefinitionsCount) : void {
            if ($alreadyDefined !== \false) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::canNotDefineSchemaWithinExtensionMessage(), $node));
                return;
            }
            if ($schemaDefinitionsCount > 0) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::schemaDefinitionNotAloneMessage(), $node));
            }
            ++$schemaDefinitionsCount;
        }];
    }
}
