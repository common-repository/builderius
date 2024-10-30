<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\ObjectFieldNode;
use Builderius\GraphQL\Language\AST\ObjectValueNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Language\AST\ValueNode;
use Builderius\GraphQL\Language\AST\VariableNode;
use Builderius\GraphQL\Language\Printer;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\EnumValueDefinition;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ValidationContext;
use Throwable;
use function array_combine;
use function array_keys;
use function array_map;
use function array_values;
use function iterator_to_array;
use function sprintf;
/**
 * Value literals of correct type
 *
 * A GraphQL document is only valid if all value literals are of the type
 * expected at their position.
 */
class ValuesOfCorrectType extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $fieldName = '';
        return [\Builderius\GraphQL\Language\AST\NodeKind::FIELD => ['enter' => static function (\Builderius\GraphQL\Language\AST\FieldNode $node) use(&$fieldName) : void {
            $fieldName = $node->name->value;
        }], \Builderius\GraphQL\Language\AST\NodeKind::NULL => static function (\Builderius\GraphQL\Language\AST\NullValueNode $node) use($context, &$fieldName) : void {
            $type = $context->getInputType();
            if (!$type instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::getBadValueMessage((string) $type, \Builderius\GraphQL\Language\Printer::doPrint($node), null, $context, $fieldName), $node));
        }, \Builderius\GraphQL\Language\AST\NodeKind::LST => function (\Builderius\GraphQL\Language\AST\ListValueNode $node) use($context, &$fieldName) : ?VisitorOperation {
            // Note: TypeInfo will traverse into a list's item type, so look to the
            // parent input type to check if it is a list.
            $type = \Builderius\GraphQL\Type\Definition\Type::getNullableType($context->getParentInputType());
            if (!$type instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
                $this->isValidScalar($context, $node, $fieldName);
                return \Builderius\GraphQL\Language\Visitor::skipNode();
            }
            return null;
        }, \Builderius\GraphQL\Language\AST\NodeKind::OBJECT => function (\Builderius\GraphQL\Language\AST\ObjectValueNode $node) use($context, &$fieldName) {
            // Note: TypeInfo will traverse into a list's item type, so look to the
            // parent input type to check if it is a list.
            $type = \Builderius\GraphQL\Type\Definition\Type::getNamedType($context->getInputType());
            if (!$type instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                $this->isValidScalar($context, $node, $fieldName);
                return \Builderius\GraphQL\Language\Visitor::skipNode();
            }
            unset($fieldName);
            // Ensure every required field exists.
            $inputFields = $type->getFields();
            $nodeFields = \iterator_to_array($node->fields);
            $fieldNodeMap = \array_combine(\array_map(static function ($field) : string {
                return $field->name->value;
            }, $nodeFields), \array_values($nodeFields));
            foreach ($inputFields as $fieldName => $fieldDef) {
                $fieldType = $fieldDef->getType();
                if (isset($fieldNodeMap[$fieldName]) || !$fieldDef->isRequired()) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::requiredFieldMessage($type->name, $fieldName, (string) $fieldType), $node));
            }
        }, \Builderius\GraphQL\Language\AST\NodeKind::OBJECT_FIELD => static function (\Builderius\GraphQL\Language\AST\ObjectFieldNode $node) use($context) : void {
            $parentType = \Builderius\GraphQL\Type\Definition\Type::getNamedType($context->getParentInputType());
            /** @var ScalarType|EnumType|InputObjectType|ListOfType|NonNull $fieldType */
            $fieldType = $context->getInputType();
            if ($fieldType || !$parentType instanceof \Builderius\GraphQL\Type\Definition\InputObjectType) {
                return;
            }
            $suggestions = \Builderius\GraphQL\Utils\Utils::suggestionList($node->name->value, \array_keys($parentType->getFields()));
            $didYouMean = $suggestions ? 'Did you mean ' . \Builderius\GraphQL\Utils\Utils::orList($suggestions) . '?' : null;
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::unknownFieldMessage($parentType->name, $node->name->value, $didYouMean), $node));
        }, \Builderius\GraphQL\Language\AST\NodeKind::ENUM => function (\Builderius\GraphQL\Language\AST\EnumValueNode $node) use($context, &$fieldName) : void {
            $type = \Builderius\GraphQL\Type\Definition\Type::getNamedType($context->getInputType());
            if (!$type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
                $this->isValidScalar($context, $node, $fieldName);
            } elseif (!$type->getValue($node->value)) {
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::getBadValueMessage($type->name, \Builderius\GraphQL\Language\Printer::doPrint($node), $this->enumTypeSuggestion($type, $node), $context, $fieldName), $node));
            }
        }, \Builderius\GraphQL\Language\AST\NodeKind::INT => function (\Builderius\GraphQL\Language\AST\IntValueNode $node) use($context, &$fieldName) : void {
            $this->isValidScalar($context, $node, $fieldName);
        }, \Builderius\GraphQL\Language\AST\NodeKind::FLOAT => function (\Builderius\GraphQL\Language\AST\FloatValueNode $node) use($context, &$fieldName) : void {
            $this->isValidScalar($context, $node, $fieldName);
        }, \Builderius\GraphQL\Language\AST\NodeKind::STRING => function (\Builderius\GraphQL\Language\AST\StringValueNode $node) use($context, &$fieldName) : void {
            $this->isValidScalar($context, $node, $fieldName);
        }, \Builderius\GraphQL\Language\AST\NodeKind::BOOLEAN => function (\Builderius\GraphQL\Language\AST\BooleanValueNode $node) use($context, &$fieldName) : void {
            $this->isValidScalar($context, $node, $fieldName);
        }];
    }
    public static function badValueMessage($typeName, $valueName, $message = null)
    {
        return \sprintf('Expected type %s, found %s', $typeName, $valueName) . ($message ? "; {$message}" : '.');
    }
    /**
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $node
     */
    private function isValidScalar(\Builderius\GraphQL\Validator\ValidationContext $context, \Builderius\GraphQL\Language\AST\ValueNode $node, $fieldName)
    {
        // Report any error at the full type expected by the location.
        /** @var ScalarType|EnumType|InputObjectType|ListOfType|NonNull $locationType */
        $locationType = $context->getInputType();
        if (!$locationType) {
            return;
        }
        $type = \Builderius\GraphQL\Type\Definition\Type::getNamedType($locationType);
        if (!$type instanceof \Builderius\GraphQL\Type\Definition\ScalarType) {
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::getBadValueMessage((string) $locationType, \Builderius\GraphQL\Language\Printer::doPrint($node), $this->enumTypeSuggestion($type, $node), $context, $fieldName), $node));
            return;
        }
        // Scalars determine if a literal value is valid via parseLiteral() which
        // may throw to indicate failure.
        try {
            $type->parseLiteral($node);
        } catch (\Throwable $error) {
            // Ensure a reference to the original error is maintained.
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::getBadValueMessage((string) $locationType, \Builderius\GraphQL\Language\Printer::doPrint($node), $error->getMessage(), $context, $fieldName), $node, null, [], null, $error));
        }
    }
    /**
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $node
     */
    private function enumTypeSuggestion($type, \Builderius\GraphQL\Language\AST\ValueNode $node)
    {
        if ($type instanceof \Builderius\GraphQL\Type\Definition\EnumType) {
            $suggestions = \Builderius\GraphQL\Utils\Utils::suggestionList(\Builderius\GraphQL\Language\Printer::doPrint($node), \array_map(static function (\Builderius\GraphQL\Type\Definition\EnumValueDefinition $value) : string {
                return $value->name;
            }, $type->getValues()));
            return $suggestions ? 'Did you mean the enum value ' . \Builderius\GraphQL\Utils\Utils::orList($suggestions) . '?' : null;
        }
    }
    public static function badArgumentValueMessage($typeName, $valueName, $fieldName, $argName, $message = null)
    {
        return \sprintf('Field "%s" argument "%s" requires type %s, found %s', $fieldName, $argName, $typeName, $valueName) . ($message ? \sprintf('; %s', $message) : '.');
    }
    public static function requiredFieldMessage($typeName, $fieldName, $fieldTypeName)
    {
        return \sprintf('Field %s.%s of required type %s was not provided.', $typeName, $fieldName, $fieldTypeName);
    }
    public static function unknownFieldMessage($typeName, $fieldName, $message = null)
    {
        return \sprintf('Field "%s" is not defined by type %s', $fieldName, $typeName) . ($message ? \sprintf('; %s', $message) : '.');
    }
    private static function getBadValueMessage($typeName, $valueName, $message = null, $context = null, $fieldName = null)
    {
        if ($context) {
            $arg = $context->getArgument();
            if ($arg) {
                return self::badArgumentValueMessage($typeName, $valueName, $fieldName, $arg->name, $message);
            }
        }
        return self::badValueMessage($typeName, $valueName, $message);
    }
}
