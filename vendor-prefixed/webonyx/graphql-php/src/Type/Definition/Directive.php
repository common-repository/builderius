<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\DirectiveLocation;
use function array_key_exists;
use function is_array;
class Directive
{
    public const DEFAULT_DEPRECATION_REASON = 'No longer supported';
    public const INCLUDE_NAME = 'include';
    public const IF_ARGUMENT_NAME = 'if';
    public const SKIP_NAME = 'skip';
    public const DEPRECATED_NAME = 'deprecated';
    public const REASON_ARGUMENT_NAME = 'reason';
    /** @var Directive[]|null */
    public static $internalDirectives;
    // Schema Definitions
    /** @var string */
    public $name;
    /** @var string|null */
    public $description;
    /** @var FieldArgument[] */
    public $args = [];
    /** @var bool */
    public $isRepeatable;
    /** @var string[] */
    public $locations;
    /** @var DirectiveDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        if (!isset($config['name'])) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Directive must be named.');
        }
        $this->name = $config['name'];
        $this->description = $config['description'] ?? null;
        if (isset($config['args'])) {
            $args = [];
            foreach ($config['args'] as $name => $arg) {
                if (\is_array($arg)) {
                    $args[] = new \Builderius\GraphQL\Type\Definition\FieldArgument($arg + ['name' => $name]);
                } else {
                    $args[] = $arg;
                }
            }
            $this->args = $args;
        }
        if (!isset($config['locations']) || !\is_array($config['locations'])) {
            throw new \Builderius\GraphQL\Error\InvariantViolation('Must provide locations for directive.');
        }
        $this->locations = $config['locations'];
        $this->isRepeatable = $config['isRepeatable'] ?? \false;
        $this->astNode = $config['astNode'] ?? null;
        $this->config = $config;
    }
    /**
     * @return Directive
     */
    public static function includeDirective()
    {
        $internal = self::getInternalDirectives();
        return $internal['include'];
    }
    /**
     * @return Directive[]
     */
    public static function getInternalDirectives() : array
    {
        if (self::$internalDirectives === null) {
            self::$internalDirectives = ['include' => new self(['name' => self::INCLUDE_NAME, 'description' => 'Directs the executor to include this field or fragment only when the `if` argument is true.', 'locations' => [\Builderius\GraphQL\Language\DirectiveLocation::FIELD, \Builderius\GraphQL\Language\DirectiveLocation::FRAGMENT_SPREAD, \Builderius\GraphQL\Language\DirectiveLocation::INLINE_FRAGMENT], 'args' => [new \Builderius\GraphQL\Type\Definition\FieldArgument(['name' => self::IF_ARGUMENT_NAME, 'type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::boolean()), 'description' => 'Included when true.'])]]), 'skip' => new self(['name' => self::SKIP_NAME, 'description' => 'Directs the executor to skip this field or fragment when the `if` argument is true.', 'locations' => [\Builderius\GraphQL\Language\DirectiveLocation::FIELD, \Builderius\GraphQL\Language\DirectiveLocation::FRAGMENT_SPREAD, \Builderius\GraphQL\Language\DirectiveLocation::INLINE_FRAGMENT], 'args' => [new \Builderius\GraphQL\Type\Definition\FieldArgument(['name' => self::IF_ARGUMENT_NAME, 'type' => \Builderius\GraphQL\Type\Definition\Type::nonNull(\Builderius\GraphQL\Type\Definition\Type::boolean()), 'description' => 'Skipped when true.'])]]), 'deprecated' => new self(['name' => self::DEPRECATED_NAME, 'description' => 'Marks an element of a GraphQL schema as no longer supported.', 'locations' => [\Builderius\GraphQL\Language\DirectiveLocation::FIELD_DEFINITION, \Builderius\GraphQL\Language\DirectiveLocation::ENUM_VALUE], 'args' => [new \Builderius\GraphQL\Type\Definition\FieldArgument(['name' => self::REASON_ARGUMENT_NAME, 'type' => \Builderius\GraphQL\Type\Definition\Type::string(), 'description' => 'Explains why this element was deprecated, usually also including a ' . 'suggestion for how to access supported similar data. Formatted using ' . 'the Markdown syntax (as specified by [CommonMark](https://commonmark.org/).', 'defaultValue' => self::DEFAULT_DEPRECATION_REASON])]])];
        }
        return self::$internalDirectives;
    }
    /**
     * @return Directive
     */
    public static function skipDirective()
    {
        $internal = self::getInternalDirectives();
        return $internal['skip'];
    }
    /**
     * @return Directive
     */
    public static function deprecatedDirective()
    {
        $internal = self::getInternalDirectives();
        return $internal['deprecated'];
    }
    /**
     * @return bool
     */
    public static function isSpecifiedDirective(\Builderius\GraphQL\Type\Definition\Directive $directive)
    {
        return \array_key_exists($directive->name, self::getInternalDirectives());
    }
}
