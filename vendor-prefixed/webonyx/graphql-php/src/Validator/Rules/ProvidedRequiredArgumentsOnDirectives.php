<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NonNullTypeNode;
use Builderius\GraphQL\Language\Printer;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\FieldArgument;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ASTValidationContext;
use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_filter;
/**
 * Provided required arguments on directives
 *
 * A directive is only valid if all required (non-null without a
 * default value) field arguments have been provided.
 */
class ProvidedRequiredArgumentsOnDirectives extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public static function missingDirectiveArgMessage(string $directiveName, string $argName, string $type)
    {
        return 'Directive "@' . $directiveName . '" argument "' . $argName . '" of type "' . $type . '" is required but not provided.';
    }
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
        $requiredArgsMap = [];
        $schema = $context->getSchema();
        $definedDirectives = $schema ? $schema->getDirectives() : \Builderius\GraphQL\Type\Definition\Directive::getInternalDirectives();
        foreach ($definedDirectives as $directive) {
            $requiredArgsMap[$directive->name] = \Builderius\GraphQL\Utils\Utils::keyMap(\array_filter($directive->args, static function (\Builderius\GraphQL\Type\Definition\FieldArgument $arg) : bool {
                return $arg->isRequired();
            }), static function (\Builderius\GraphQL\Type\Definition\FieldArgument $arg) : string {
                return $arg->name;
            });
        }
        $astDefinition = $context->getDocument()->definitions;
        foreach ($astDefinition as $def) {
            if (!$def instanceof \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode) {
                continue;
            }
            $arguments = $def->arguments ?? [];
            $requiredArgsMap[$def->name->value] = \Builderius\GraphQL\Utils\Utils::keyMap(\Builderius\GraphQL\Utils\Utils::filter($arguments, static function (\Builderius\GraphQL\Language\AST\InputValueDefinitionNode $argument) : bool {
                return $argument->type instanceof \Builderius\GraphQL\Language\AST\NonNullTypeNode && (!isset($argument->defaultValue) || $argument->defaultValue === null);
            }), static function (\Builderius\GraphQL\Language\AST\InputValueDefinitionNode $argument) : string {
                return $argument->name->value;
            });
        }
        return [\Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE => [
            // Validate on leave to allow for deeper errors to appear first.
            'leave' => static function (\Builderius\GraphQL\Language\AST\DirectiveNode $directiveNode) use($requiredArgsMap, $context) : ?string {
                $directiveName = $directiveNode->name->value;
                $requiredArgs = $requiredArgsMap[$directiveName] ?? null;
                if (!$requiredArgs) {
                    return null;
                }
                $argNodes = $directiveNode->arguments ?? [];
                $argNodeMap = \Builderius\GraphQL\Utils\Utils::keyMap($argNodes, static function (\Builderius\GraphQL\Language\AST\ArgumentNode $arg) : string {
                    return $arg->name->value;
                });
                foreach ($requiredArgs as $argName => $arg) {
                    if (isset($argNodeMap[$argName])) {
                        continue;
                    }
                    if ($arg instanceof \Builderius\GraphQL\Type\Definition\FieldArgument) {
                        $argType = (string) $arg->getType();
                    } elseif ($arg instanceof \Builderius\GraphQL\Language\AST\InputValueDefinitionNode) {
                        $argType = \Builderius\GraphQL\Language\Printer::doPrint($arg->type);
                    } else {
                        $argType = '';
                    }
                    $context->reportError(new \Builderius\GraphQL\Error\Error(static::missingDirectiveArgMessage($directiveName, $argName, $argType), [$directiveNode]));
                }
                return null;
            },
        ]];
    }
}
