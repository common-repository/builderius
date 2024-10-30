<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language;

use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumTypeExtensionNode;
use Builderius\GraphQL\Language\AST\EnumValueDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\FieldDefinitionNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\ListTypeNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\NamedTypeNode;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NonNullTypeNode;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\ObjectFieldNode;
use Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\ObjectValueNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\OperationTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode;
use Builderius\GraphQL\Language\AST\SchemaDefinitionNode;
use Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\UnionTypeExtensionNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Language\AST\VariableNode;
use Builderius\GraphQL\Utils\Utils;
use function count;
use function implode;
use function json_encode;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
/**
 * Prints AST to string. Capable of printing GraphQL queries and Type definition language.
 * Useful for pretty-printing queries or printing back AST for logging, documentation, etc.
 *
 * Usage example:
 *
 * ```php
 * $query = 'query myQuery {someField}';
 * $ast = GraphQL\Language\Parser::parse($query);
 * $printed = GraphQL\Language\Printer::doPrint($ast);
 * ```
 */
class Printer
{
    /**
     * Prints AST to string. Capable of printing GraphQL queries and Type definition language.
     *
     * @param Node $ast
     *
     * @return string
     *
     * @api
     */
    public static function doPrint($ast)
    {
        static $instance;
        $instance = $instance ?? new static();
        return $instance->printAST($ast);
    }
    protected function __construct()
    {
    }
    /**
     * Traverse an AST bottom-up, converting all nodes to strings.
     *
     * That means the AST is manipulated in such a way that it no longer
     * resembles the well-formed result of parsing.
     */
    public function printAST($ast)
    {
        return \Builderius\GraphQL\Language\Visitor::visit($ast, ['leave' => [\Builderius\GraphQL\Language\AST\NodeKind::NAME => static function (\Builderius\GraphQL\Language\AST\NameNode $node) : string {
            return $node->value;
        }, \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE => static function (\Builderius\GraphQL\Language\AST\VariableNode $node) : string {
            return '$' . $node->name;
        }, \Builderius\GraphQL\Language\AST\NodeKind::DOCUMENT => function (\Builderius\GraphQL\Language\AST\DocumentNode $node) : string {
            return $this->join($node->definitions, "\n\n") . "\n";
        }, \Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $node) : string {
            $op = $node->operation;
            $name = $node->name;
            $varDefs = $this->wrap('(', $this->join($node->variableDefinitions, ', '), ')');
            $directives = $this->join($node->directives, ' ');
            $selectionSet = $node->selectionSet;
            // Anonymous queries with no directives or variable definitions can use
            // the query short form.
            return $name === null && \strlen($directives ?? '') === 0 && !$varDefs && $op === 'query' ? $selectionSet : $this->join([$op, $this->join([$name, $varDefs]), $directives, $selectionSet], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => function (\Builderius\GraphQL\Language\AST\VariableDefinitionNode $node) : string {
            return $node->variable . ': ' . $node->type . $this->wrap(' = ', $node->defaultValue) . $this->wrap(' ', $this->join($node->directives, ' '));
        }, \Builderius\GraphQL\Language\AST\NodeKind::SELECTION_SET => function (\Builderius\GraphQL\Language\AST\SelectionSetNode $node) {
            return $this->block($node->selections);
        }, \Builderius\GraphQL\Language\AST\NodeKind::FIELD => function (\Builderius\GraphQL\Language\AST\FieldNode $node) : string {
            return $this->join([$this->wrap('', $node->alias, ': ') . $node->name . $this->wrap('(', $this->join($node->arguments, ', '), ')'), $this->join($node->directives, ' '), $node->selectionSet], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::ARGUMENT => static function (\Builderius\GraphQL\Language\AST\ArgumentNode $node) : string {
            return $node->name . ': ' . $node->value;
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_SPREAD => function (\Builderius\GraphQL\Language\AST\FragmentSpreadNode $node) : string {
            return '...' . $node->name . $this->wrap(' ', $this->join($node->directives, ' '));
        }, \Builderius\GraphQL\Language\AST\NodeKind::INLINE_FRAGMENT => function (\Builderius\GraphQL\Language\AST\InlineFragmentNode $node) : string {
            return $this->join(['...', $this->wrap('on ', $node->typeCondition), $this->join($node->directives, ' '), $node->selectionSet], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => function (\Builderius\GraphQL\Language\AST\FragmentDefinitionNode $node) : string {
            // Note: fragment variable definitions are experimental and may be changed or removed in the future.
            return \sprintf('fragment %s', $node->name) . $this->wrap('(', $this->join($node->variableDefinitions, ', '), ')') . \sprintf(' on %s ', $node->typeCondition) . $this->wrap('', $this->join($node->directives, ' '), ' ') . $node->selectionSet;
        }, \Builderius\GraphQL\Language\AST\NodeKind::INT => static function (\Builderius\GraphQL\Language\AST\IntValueNode $node) : string {
            return $node->value;
        }, \Builderius\GraphQL\Language\AST\NodeKind::FLOAT => static function (\Builderius\GraphQL\Language\AST\FloatValueNode $node) : string {
            return $node->value;
        }, \Builderius\GraphQL\Language\AST\NodeKind::STRING => function (\Builderius\GraphQL\Language\AST\StringValueNode $node, $key) : string {
            if ($node->block) {
                return $this->printBlockString($node->value, $key === 'description');
            }
            return \json_encode($node->value);
        }, \Builderius\GraphQL\Language\AST\NodeKind::BOOLEAN => static function (\Builderius\GraphQL\Language\AST\BooleanValueNode $node) : string {
            return $node->value ? 'true' : 'false';
        }, \Builderius\GraphQL\Language\AST\NodeKind::NULL => static function (\Builderius\GraphQL\Language\AST\NullValueNode $node) : string {
            return 'null';
        }, \Builderius\GraphQL\Language\AST\NodeKind::ENUM => static function (\Builderius\GraphQL\Language\AST\EnumValueNode $node) : string {
            return $node->value;
        }, \Builderius\GraphQL\Language\AST\NodeKind::LST => function (\Builderius\GraphQL\Language\AST\ListValueNode $node) : string {
            return '[' . $this->join($node->values, ', ') . ']';
        }, \Builderius\GraphQL\Language\AST\NodeKind::OBJECT => function (\Builderius\GraphQL\Language\AST\ObjectValueNode $node) : string {
            return '{' . $this->join($node->fields, ', ') . '}';
        }, \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_FIELD => static function (\Builderius\GraphQL\Language\AST\ObjectFieldNode $node) : string {
            return $node->name . ': ' . $node->value;
        }, \Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE => function (\Builderius\GraphQL\Language\AST\DirectiveNode $node) : string {
            return '@' . $node->name . $this->wrap('(', $this->join($node->arguments, ', '), ')');
        }, \Builderius\GraphQL\Language\AST\NodeKind::NAMED_TYPE => static function (\Builderius\GraphQL\Language\AST\NamedTypeNode $node) : string {
            // @phpstan-ignore-next-line the printer works bottom up, so this is already a string here
            return $node->name;
        }, \Builderius\GraphQL\Language\AST\NodeKind::LIST_TYPE => static function (\Builderius\GraphQL\Language\AST\ListTypeNode $node) : string {
            return '[' . $node->type . ']';
        }, \Builderius\GraphQL\Language\AST\NodeKind::NON_NULL_TYPE => static function (\Builderius\GraphQL\Language\AST\NonNullTypeNode $node) : string {
            return $node->type . '!';
        }, \Builderius\GraphQL\Language\AST\NodeKind::SCHEMA_DEFINITION => function (\Builderius\GraphQL\Language\AST\SchemaDefinitionNode $def) : string {
            return $this->join(['schema', $this->join($def->directives, ' '), $this->block($def->operationTypes)], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::OPERATION_TYPE_DEFINITION => static function (\Builderius\GraphQL\Language\AST\OperationTypeDefinitionNode $def) : string {
            return $def->operation . ': ' . $def->type;
        }, \Builderius\GraphQL\Language\AST\NodeKind::SCALAR_TYPE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode $def) : string {
            return $this->join(['scalar', $def->name, $this->join($def->directives, ' ')], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_TYPE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode $def) : string {
            return $this->join(['type', $def->name, $this->wrap('implements ', $this->join($def->interfaces, ' & ')), $this->join($def->directives, ' '), $this->block($def->fields)], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::FIELD_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\FieldDefinitionNode $def) : string {
            $noIndent = \Builderius\GraphQL\Utils\Utils::every($def->arguments, static function (string $arg) : bool {
                return \strpos($arg, "\n") === \false;
            });
            return $def->name . ($noIndent ? $this->wrap('(', $this->join($def->arguments, ', '), ')') : $this->wrap("(\n", $this->indent($this->join($def->arguments, "\n")), "\n)")) . ': ' . $def->type . $this->wrap(' ', $this->join($def->directives, ' '));
        }), \Builderius\GraphQL\Language\AST\NodeKind::INPUT_VALUE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\InputValueDefinitionNode $def) : string {
            return $this->join([$def->name . ': ' . $def->type, $this->wrap('= ', $def->defaultValue), $this->join($def->directives, ' ')], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode $def) : string {
            return $this->join(['interface', $def->name, $this->join($def->directives, ' '), $this->block($def->fields)], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::UNION_TYPE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode $def) : string {
            return $this->join(['union', $def->name, $this->join($def->directives, ' '), \count($def->types ?? []) > 0 ? '= ' . $this->join($def->types, ' | ') : ''], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::ENUM_TYPE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode $def) : string {
            return $this->join(['enum', $def->name, $this->join($def->directives, ' '), $this->block($def->values)], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::ENUM_VALUE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\EnumValueDefinitionNode $def) : string {
            return $this->join([$def->name, $this->join($def->directives, ' ')], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode $def) : string {
            return $this->join(['input', $def->name, $this->join($def->directives, ' '), $this->block($def->fields)], ' ');
        }), \Builderius\GraphQL\Language\AST\NodeKind::SCHEMA_EXTENSION => function (\Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode $def) : string {
            return $this->join(['extend schema', $this->join($def->directives, ' '), $this->block($def->operationTypes)], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::SCALAR_TYPE_EXTENSION => function (\Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode $def) : string {
            return $this->join(['extend scalar', $def->name, $this->join($def->directives, ' ')], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_TYPE_EXTENSION => function (\Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode $def) : string {
            return $this->join(['extend type', $def->name, $this->wrap('implements ', $this->join($def->interfaces, ' & ')), $this->join($def->directives, ' '), $this->block($def->fields)], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_EXTENSION => function (\Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode $def) : string {
            return $this->join(['extend interface', $def->name, $this->join($def->directives, ' '), $this->block($def->fields)], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::UNION_TYPE_EXTENSION => function (\Builderius\GraphQL\Language\AST\UnionTypeExtensionNode $def) : string {
            return $this->join(['extend union', $def->name, $this->join($def->directives, ' '), \count($def->types ?? []) > 0 ? '= ' . $this->join($def->types, ' | ') : ''], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::ENUM_TYPE_EXTENSION => function (\Builderius\GraphQL\Language\AST\EnumTypeExtensionNode $def) : string {
            return $this->join(['extend enum', $def->name, $this->join($def->directives, ' '), $this->block($def->values)], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_EXTENSION => function (\Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode $def) : string {
            return $this->join(['extend input', $def->name, $this->join($def->directives, ' '), $this->block($def->fields)], ' ');
        }, \Builderius\GraphQL\Language\AST\NodeKind::DIRECTIVE_DEFINITION => $this->addDescription(function (\Builderius\GraphQL\Language\AST\DirectiveDefinitionNode $def) : string {
            $noIndent = \Builderius\GraphQL\Utils\Utils::every($def->arguments, static function (string $arg) : bool {
                return \strpos($arg, "\n") === \false;
            });
            return 'directive @' . $def->name . ($noIndent ? $this->wrap('(', $this->join($def->arguments, ', '), ')') : $this->wrap("(\n", $this->indent($this->join($def->arguments, "\n")), "\n")) . ($def->repeatable ? ' repeatable' : '') . ' on ' . $this->join($def->locations, ' | ');
        })]]);
    }
    public function addDescription(callable $cb)
    {
        return function ($node) use($cb) : string {
            return $this->join([$node->description, $cb($node)], "\n");
        };
    }
    /**
     * If maybeString is not null or empty, then wrap with start and end, otherwise
     * print an empty string.
     */
    public function wrap($start, $maybeString, $end = '')
    {
        return $maybeString ? $start . $maybeString . $end : '';
    }
    /**
     * Given array, print each item on its own line, wrapped in an
     * indented "{ }" block.
     */
    public function block($array)
    {
        return $array && $this->length($array) ? "{\n" . $this->indent($this->join($array, "\n")) . "\n}" : '';
    }
    public function indent($maybeString)
    {
        return $maybeString ? '  ' . \str_replace("\n", "\n  ", $maybeString) : '';
    }
    public function manyList($start, $list, $separator, $end)
    {
        return $this->length($list) === 0 ? null : $start . $this->join($list, $separator) . $end;
    }
    public function length($maybeArray)
    {
        return $maybeArray ? \count($maybeArray) : 0;
    }
    public function join($maybeArray, $separator = '') : string
    {
        return $maybeArray ? \implode($separator, \Builderius\GraphQL\Utils\Utils::filter($maybeArray, static function ($x) : bool {
            return (bool) $x;
        })) : '';
    }
    /**
     * Print a block string in the indented block form by adding a leading and
     * trailing blank line. However, if a block string starts with whitespace and is
     * a single-line, adding a leading blank line would strip that whitespace.
     */
    private function printBlockString($value, $isDescription)
    {
        $escaped = \str_replace('"""', '\\"""', $value);
        return ($value[0] === ' ' || $value[0] === "\t") && \strpos($value, "\n") === \false ? '"""' . \preg_replace('/"$/', "\"\n", $escaped) . '"""' : '"""' . "\n" . ($isDescription ? $escaped : $this->indent($escaped)) . "\n" . '"""';
    }
}
