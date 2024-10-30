<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Validator\ASTValidationContext;
use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
/**
 * Unique directive names per location
 *
 * A GraphQL document is only valid if all non-repeatable directives at
 * a given location are uniquely named.
 */
class UniqueDirectivesPerLocation extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
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
        $uniqueDirectiveMap = [];
        $schema = $context->getSchema();
        $definedDirectives = $schema !== null ? $schema->getDirectives() : \Builderius\GraphQL\Type\Definition\Directive::getInternalDirectives();
        foreach ($definedDirectives as $directive) {
            $uniqueDirectiveMap[$directive->name] = !$directive->isRepeatable;
        }
        $astDefinitions = $context->getDocument()->definitions;
        foreach ($astDefinitions as $definition) {
            if (!$definition instanceof \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode) {
                continue;
            }
            $uniqueDirectiveMap[$definition->name->value] = $definition->repeatable;
        }
        return ['enter' => static function (\Builderius\GraphQL\Language\AST\Node $node) use($uniqueDirectiveMap, $context) : void {
            if (!isset($node->directives)) {
                return;
            }
            $knownDirectives = [];
            /** @var DirectiveNode $directive */
            foreach ($node->directives as $directive) {
                $directiveName = $directive->name->value;
                if (!isset($uniqueDirectiveMap[$directiveName])) {
                    continue;
                }
                if (isset($knownDirectives[$directiveName])) {
                    $context->reportError(new \Builderius\GraphQL\Error\Error(self::duplicateDirectiveMessage($directiveName), [$knownDirectives[$directiveName], $directive]));
                } else {
                    $knownDirectives[$directiveName] = $directive;
                }
            }
        }];
    }
    public static function duplicateDirectiveMessage($directiveName)
    {
        return \sprintf('The directive "%s" can only be used once at this location.', $directiveName);
    }
}
