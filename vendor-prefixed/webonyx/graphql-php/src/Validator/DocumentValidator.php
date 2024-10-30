<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator;

use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\TypeInfo;
use Builderius\GraphQL\Validator\Rules\DisableIntrospection;
use Builderius\GraphQL\Validator\Rules\ExecutableDefinitions;
use Builderius\GraphQL\Validator\Rules\FieldsOnCorrectType;
use Builderius\GraphQL\Validator\Rules\FragmentsOnCompositeTypes;
use Builderius\GraphQL\Validator\Rules\KnownArgumentNames;
use Builderius\GraphQL\Validator\Rules\KnownArgumentNamesOnDirectives;
use Builderius\GraphQL\Validator\Rules\KnownDirectives;
use Builderius\GraphQL\Validator\Rules\KnownFragmentNames;
use Builderius\GraphQL\Validator\Rules\KnownTypeNames;
use Builderius\GraphQL\Validator\Rules\LoneAnonymousOperation;
use Builderius\GraphQL\Validator\Rules\LoneSchemaDefinition;
use Builderius\GraphQL\Validator\Rules\NoFragmentCycles;
use Builderius\GraphQL\Validator\Rules\NoUndefinedVariables;
use Builderius\GraphQL\Validator\Rules\NoUnusedFragments;
use Builderius\GraphQL\Validator\Rules\NoUnusedVariables;
use Builderius\GraphQL\Validator\Rules\OverlappingFieldsCanBeMerged;
use Builderius\GraphQL\Validator\Rules\PossibleFragmentSpreads;
use Builderius\GraphQL\Validator\Rules\ProvidedRequiredArguments;
use Builderius\GraphQL\Validator\Rules\ProvidedRequiredArgumentsOnDirectives;
use Builderius\GraphQL\Validator\Rules\QueryComplexity;
use Builderius\GraphQL\Validator\Rules\QueryDepth;
use Builderius\GraphQL\Validator\Rules\QuerySecurityRule;
use Builderius\GraphQL\Validator\Rules\ScalarLeafs;
use Builderius\GraphQL\Validator\Rules\SingleFieldSubscription;
use Builderius\GraphQL\Validator\Rules\UniqueArgumentNames;
use Builderius\GraphQL\Validator\Rules\UniqueDirectivesPerLocation;
use Builderius\GraphQL\Validator\Rules\UniqueFragmentNames;
use Builderius\GraphQL\Validator\Rules\UniqueInputFieldNames;
use Builderius\GraphQL\Validator\Rules\UniqueOperationNames;
use Builderius\GraphQL\Validator\Rules\UniqueVariableNames;
use Builderius\GraphQL\Validator\Rules\ValidationRule;
use Builderius\GraphQL\Validator\Rules\ValuesOfCorrectType;
use Builderius\GraphQL\Validator\Rules\VariablesAreInputTypes;
use Builderius\GraphQL\Validator\Rules\VariablesInAllowedPosition;
use Throwable;
use function array_filter;
use function array_merge;
use function count;
use function is_array;
use function sprintf;
/**
 * Implements the "Validation" section of the spec.
 *
 * Validation runs synchronously, returning an array of encountered errors, or
 * an empty array if no errors were encountered and the document is valid.
 *
 * A list of specific validation rules may be provided. If not provided, the
 * default list of rules defined by the GraphQL specification will be used.
 *
 * Each validation rule is an instance of GraphQL\Validator\Rules\ValidationRule
 * which returns a visitor (see the [GraphQL\Language\Visitor API](reference.md#graphqllanguagevisitor)).
 *
 * Visitor methods are expected to return an instance of [GraphQL\Error\Error](reference.md#graphqlerrorerror),
 * or array of such instances when invalid.
 *
 * Optionally a custom TypeInfo instance may be provided. If not provided, one
 * will be created from the provided schema.
 */
class DocumentValidator
{
    /** @var ValidationRule[] */
    private static $rules = [];
    /** @var ValidationRule[]|null */
    private static $defaultRules;
    /** @var QuerySecurityRule[]|null */
    private static $securityRules;
    /** @var ValidationRule[]|null */
    private static $sdlRules;
    /** @var bool */
    private static $initRules = \false;
    /**
     * Primary method for query validation. See class description for details.
     *
     * @param ValidationRule[]|null $rules
     *
     * @return Error[]
     *
     * @api
     */
    public static function validate(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $ast, ?array $rules = null, ?\Builderius\GraphQL\Utils\TypeInfo $typeInfo = null)
    {
        if ($rules === null) {
            $rules = static::allRules();
        }
        if (\is_array($rules) === \true && \count($rules) === 0) {
            // Skip validation if there are no rules
            return [];
        }
        $typeInfo = $typeInfo ?? new \Builderius\GraphQL\Utils\TypeInfo($schema);
        return static::visitUsingRules($schema, $typeInfo, $ast, $rules);
    }
    /**
     * Returns all global validation rules.
     *
     * @return ValidationRule[]
     *
     * @api
     */
    public static function allRules()
    {
        if (!self::$initRules) {
            static::$rules = \array_merge(static::defaultRules(), self::securityRules(), self::$rules);
            static::$initRules = \true;
        }
        return self::$rules;
    }
    public static function defaultRules()
    {
        if (self::$defaultRules === null) {
            self::$defaultRules = [\Builderius\GraphQL\Validator\Rules\ExecutableDefinitions::class => new \Builderius\GraphQL\Validator\Rules\ExecutableDefinitions(), \Builderius\GraphQL\Validator\Rules\UniqueOperationNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueOperationNames(), \Builderius\GraphQL\Validator\Rules\LoneAnonymousOperation::class => new \Builderius\GraphQL\Validator\Rules\LoneAnonymousOperation(), \Builderius\GraphQL\Validator\Rules\SingleFieldSubscription::class => new \Builderius\GraphQL\Validator\Rules\SingleFieldSubscription(), \Builderius\GraphQL\Validator\Rules\KnownTypeNames::class => new \Builderius\GraphQL\Validator\Rules\KnownTypeNames(), \Builderius\GraphQL\Validator\Rules\FragmentsOnCompositeTypes::class => new \Builderius\GraphQL\Validator\Rules\FragmentsOnCompositeTypes(), \Builderius\GraphQL\Validator\Rules\VariablesAreInputTypes::class => new \Builderius\GraphQL\Validator\Rules\VariablesAreInputTypes(), \Builderius\GraphQL\Validator\Rules\ScalarLeafs::class => new \Builderius\GraphQL\Validator\Rules\ScalarLeafs(), \Builderius\GraphQL\Validator\Rules\FieldsOnCorrectType::class => new \Builderius\GraphQL\Validator\Rules\FieldsOnCorrectType(), \Builderius\GraphQL\Validator\Rules\UniqueFragmentNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueFragmentNames(), \Builderius\GraphQL\Validator\Rules\KnownFragmentNames::class => new \Builderius\GraphQL\Validator\Rules\KnownFragmentNames(), \Builderius\GraphQL\Validator\Rules\NoUnusedFragments::class => new \Builderius\GraphQL\Validator\Rules\NoUnusedFragments(), \Builderius\GraphQL\Validator\Rules\PossibleFragmentSpreads::class => new \Builderius\GraphQL\Validator\Rules\PossibleFragmentSpreads(), \Builderius\GraphQL\Validator\Rules\NoFragmentCycles::class => new \Builderius\GraphQL\Validator\Rules\NoFragmentCycles(), \Builderius\GraphQL\Validator\Rules\UniqueVariableNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueVariableNames(), \Builderius\GraphQL\Validator\Rules\NoUndefinedVariables::class => new \Builderius\GraphQL\Validator\Rules\NoUndefinedVariables(), \Builderius\GraphQL\Validator\Rules\NoUnusedVariables::class => new \Builderius\GraphQL\Validator\Rules\NoUnusedVariables(), \Builderius\GraphQL\Validator\Rules\KnownDirectives::class => new \Builderius\GraphQL\Validator\Rules\KnownDirectives(), \Builderius\GraphQL\Validator\Rules\UniqueDirectivesPerLocation::class => new \Builderius\GraphQL\Validator\Rules\UniqueDirectivesPerLocation(), \Builderius\GraphQL\Validator\Rules\KnownArgumentNames::class => new \Builderius\GraphQL\Validator\Rules\KnownArgumentNames(), \Builderius\GraphQL\Validator\Rules\UniqueArgumentNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueArgumentNames(), \Builderius\GraphQL\Validator\Rules\ValuesOfCorrectType::class => new \Builderius\GraphQL\Validator\Rules\ValuesOfCorrectType(), \Builderius\GraphQL\Validator\Rules\ProvidedRequiredArguments::class => new \Builderius\GraphQL\Validator\Rules\ProvidedRequiredArguments(), \Builderius\GraphQL\Validator\Rules\VariablesInAllowedPosition::class => new \Builderius\GraphQL\Validator\Rules\VariablesInAllowedPosition(), \Builderius\GraphQL\Validator\Rules\OverlappingFieldsCanBeMerged::class => new \Builderius\GraphQL\Validator\Rules\OverlappingFieldsCanBeMerged(), \Builderius\GraphQL\Validator\Rules\UniqueInputFieldNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueInputFieldNames()];
        }
        return self::$defaultRules;
    }
    /**
     * @return QuerySecurityRule[]
     */
    public static function securityRules()
    {
        // This way of defining rules is deprecated
        // When custom security rule is required - it should be just added via DocumentValidator::addRule();
        // TODO: deprecate this
        if (self::$securityRules === null) {
            self::$securityRules = [
                \Builderius\GraphQL\Validator\Rules\DisableIntrospection::class => new \Builderius\GraphQL\Validator\Rules\DisableIntrospection(\Builderius\GraphQL\Validator\Rules\DisableIntrospection::DISABLED),
                // DEFAULT DISABLED
                \Builderius\GraphQL\Validator\Rules\QueryDepth::class => new \Builderius\GraphQL\Validator\Rules\QueryDepth(\Builderius\GraphQL\Validator\Rules\QueryDepth::DISABLED),
                // default disabled
                \Builderius\GraphQL\Validator\Rules\QueryComplexity::class => new \Builderius\GraphQL\Validator\Rules\QueryComplexity(\Builderius\GraphQL\Validator\Rules\QueryComplexity::DISABLED),
            ];
        }
        return self::$securityRules;
    }
    public static function sdlRules()
    {
        if (self::$sdlRules === null) {
            self::$sdlRules = [\Builderius\GraphQL\Validator\Rules\LoneSchemaDefinition::class => new \Builderius\GraphQL\Validator\Rules\LoneSchemaDefinition(), \Builderius\GraphQL\Validator\Rules\KnownDirectives::class => new \Builderius\GraphQL\Validator\Rules\KnownDirectives(), \Builderius\GraphQL\Validator\Rules\KnownArgumentNamesOnDirectives::class => new \Builderius\GraphQL\Validator\Rules\KnownArgumentNamesOnDirectives(), \Builderius\GraphQL\Validator\Rules\UniqueDirectivesPerLocation::class => new \Builderius\GraphQL\Validator\Rules\UniqueDirectivesPerLocation(), \Builderius\GraphQL\Validator\Rules\UniqueArgumentNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueArgumentNames(), \Builderius\GraphQL\Validator\Rules\UniqueInputFieldNames::class => new \Builderius\GraphQL\Validator\Rules\UniqueInputFieldNames(), \Builderius\GraphQL\Validator\Rules\ProvidedRequiredArgumentsOnDirectives::class => new \Builderius\GraphQL\Validator\Rules\ProvidedRequiredArgumentsOnDirectives()];
        }
        return self::$sdlRules;
    }
    /**
     * This uses a specialized visitor which runs multiple visitors in parallel,
     * while maintaining the visitor skip and break API.
     *
     * @param ValidationRule[] $rules
     *
     * @return Error[]
     */
    public static function visitUsingRules(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Utils\TypeInfo $typeInfo, \Builderius\GraphQL\Language\AST\DocumentNode $documentNode, array $rules)
    {
        $context = new \Builderius\GraphQL\Validator\ValidationContext($schema, $documentNode, $typeInfo);
        $visitors = [];
        foreach ($rules as $rule) {
            $visitors[] = $rule->getVisitor($context);
        }
        \Builderius\GraphQL\Language\Visitor::visit($documentNode, \Builderius\GraphQL\Language\Visitor::visitWithTypeInfo($typeInfo, \Builderius\GraphQL\Language\Visitor::visitInParallel($visitors)));
        return $context->getErrors();
    }
    /**
     * Returns global validation rule by name. Standard rules are named by class name, so
     * example usage for such rules:
     *
     * $rule = DocumentValidator::getRule(GraphQL\Validator\Rules\QueryComplexity::class);
     *
     * @param string $name
     *
     * @return ValidationRule
     *
     * @api
     */
    public static function getRule($name)
    {
        $rules = static::allRules();
        if (isset($rules[$name])) {
            return $rules[$name];
        }
        $name = \sprintf('GraphQL\\Validator\\Rules\\%s', $name);
        return $rules[$name] ?? null;
    }
    /**
     * Add rule to list of global validation rules
     *
     * @api
     */
    public static function addRule(\Builderius\GraphQL\Validator\Rules\ValidationRule $rule)
    {
        self::$rules[$rule->getName()] = $rule;
    }
    public static function isError($value)
    {
        return \is_array($value) ? \count(\array_filter($value, static function ($item) : bool {
            return $item instanceof \Throwable;
        })) === \count($value) : $value instanceof \Throwable;
    }
    public static function append(&$arr, $items)
    {
        if (\is_array($items)) {
            $arr = \array_merge($arr, $items);
        } else {
            $arr[] = $items;
        }
        return $arr;
    }
    /**
     * Utility which determines if a value literal node is valid for an input type.
     *
     * Deprecated. Rely on validation for documents co
     * ntaining literal values.
     *
     * @deprecated
     *
     * @return Error[]
     */
    public static function isValidLiteralValue(\Builderius\GraphQL\Type\Definition\Type $type, $valueNode)
    {
        $emptySchema = new \Builderius\GraphQL\Type\Schema([]);
        $emptyDoc = new \Builderius\GraphQL\Language\AST\DocumentNode(['definitions' => []]);
        $typeInfo = new \Builderius\GraphQL\Utils\TypeInfo($emptySchema, $type);
        $context = new \Builderius\GraphQL\Validator\ValidationContext($emptySchema, $emptyDoc, $typeInfo);
        $validator = new \Builderius\GraphQL\Validator\Rules\ValuesOfCorrectType();
        $visitor = $validator->getVisitor($context);
        \Builderius\GraphQL\Language\Visitor::visit($valueNode, \Builderius\GraphQL\Language\Visitor::visitWithTypeInfo($typeInfo, $visitor));
        return $context->getErrors();
    }
    /**
     * @param ValidationRule[]|null $rules
     *
     * @return Error[]
     *
     * @throws Exception
     */
    public static function validateSDL(\Builderius\GraphQL\Language\AST\DocumentNode $documentAST, ?\Builderius\GraphQL\Type\Schema $schemaToExtend = null, ?array $rules = null)
    {
        $usedRules = $rules ?? self::sdlRules();
        $context = new \Builderius\GraphQL\Validator\SDLValidationContext($documentAST, $schemaToExtend);
        $visitors = [];
        foreach ($usedRules as $rule) {
            $visitors[] = $rule->getSDLVisitor($context);
        }
        \Builderius\GraphQL\Language\Visitor::visit($documentAST, \Builderius\GraphQL\Language\Visitor::visitInParallel($visitors));
        return $context->getErrors();
    }
    public static function assertValidSDL(\Builderius\GraphQL\Language\AST\DocumentNode $documentAST)
    {
        $errors = self::validateSDL($documentAST);
        if (\count($errors) > 0) {
            throw new \Builderius\GraphQL\Error\Error(self::combineErrorMessages($errors));
        }
    }
    public static function assertValidSDLExtension(\Builderius\GraphQL\Language\AST\DocumentNode $documentAST, \Builderius\GraphQL\Type\Schema $schema)
    {
        $errors = self::validateSDL($documentAST, $schema);
        if (\count($errors) > 0) {
            throw new \Builderius\GraphQL\Error\Error(self::combineErrorMessages($errors));
        }
    }
    /**
     * @param Error[] $errors
     */
    private static function combineErrorMessages(array $errors) : string
    {
        $str = '';
        foreach ($errors as $error) {
            $str .= $error->getMessage() . "\n\n";
        }
        return $str;
    }
}
