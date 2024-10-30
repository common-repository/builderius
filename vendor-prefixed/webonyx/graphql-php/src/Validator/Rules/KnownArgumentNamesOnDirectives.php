<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\FieldArgument;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ASTValidationContext;
use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_map;
use function in_array;
use function sprintf;
/**
 * Known argument names on directives
 *
 * A GraphQL directive is only valid if all supplied arguments are defined by
 * that field.
 */
class KnownArgumentNamesOnDirectives extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /**
     * @param string[] $suggestedArgs
     */
    public static function unknownDirectiveArgMessage($argName, $directiveName, array $suggestedArgs)
    {
        $message = \sprintf('Unknown argument "%s" on directive "@%s".', $argName, $directiveName);
        if (isset($suggestedArgs[0])) {
            $message .= \sprintf(' Did you mean %s?', \Builderius\GraphQL\Utils\Utils::quotedOrList($suggestedArgs));
        }
        return $message;
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
        $directiveArgs = [];
        $schema = $context->getSchema();
        $definedDirectives = $schema !== null ? $schema->getDirectives() : \Builderius\GraphQL\Type\Definition\Directive::getInternalDirectives();
        foreach ($definedDirectives as $directive) {
            $directiveArgs[$directive->name] = \array_map(static function (\Builderius\GraphQL\Type\Definition\FieldArgument $arg) : string {
                return $arg->name;
            }, $directive->args);
        }
        $astDefinitions = $context->getDocument()->definitions;
        foreach ($astDefinitions as $def) {
            if (!$def instanceof \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode) {
                continue;
            }
            $name = $def->name->value;
            if ($def->arguments !== null) {
                $directiveArgs[$name] = \Builderius\GraphQL\Utils\Utils::map($def->arguments ?? [], static function (\Builderius\GraphQL\Language\AST\InputValueDefinitionNode $arg) : string {
                    return $arg->name->value;
                });
            } else {
                $directiveArgs[$name] = [];
            }
        }
        return [\Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE => static function (\Builderius\GraphQL\Language\AST\DirectiveNode $directiveNode) use($directiveArgs, $context) : VisitorOperation {
            $directiveName = $directiveNode->name->value;
            $knownArgs = $directiveArgs[$directiveName] ?? null;
            if ($directiveNode->arguments === null || $knownArgs === null) {
                return \Builderius\GraphQL\Language\Visitor::skipNode();
            }
            foreach ($directiveNode->arguments as $argNode) {
                $argName = $argNode->name->value;
                if (\in_array($argName, $knownArgs, \true)) {
                    continue;
                }
                $suggestions = \Builderius\GraphQL\Utils\Utils::suggestionList($argName, $knownArgs);
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::unknownDirectiveArgMessage($argName, $directiveName, $suggestions), [$argNode]));
            }
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
}
