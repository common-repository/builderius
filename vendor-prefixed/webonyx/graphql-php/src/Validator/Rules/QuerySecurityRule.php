<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use ArrayObject;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Utils\TypeInfo;
use Builderius\GraphQL\Validator\ValidationContext;
use InvalidArgumentException;
use function class_alias;
use function method_exists;
use function sprintf;
abstract class QuerySecurityRule extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public const DISABLED = 0;
    /** @var FragmentDefinitionNode[] */
    private $fragments = [];
    /**
     * check if equal to 0 no check is done. Must be greater or equal to 0.
     *
     * @param string $name
     * @param int    $value
     */
    protected function checkIfGreaterOrEqualToZero($name, $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException(\sprintf('$%s argument must be greater or equal to 0.', $name));
        }
    }
    protected function getFragment(\Builderius\GraphQL\Language\AST\FragmentSpreadNode $fragmentSpread)
    {
        $spreadName = $fragmentSpread->name->value;
        $fragments = $this->getFragments();
        return $fragments[$spreadName] ?? null;
    }
    /**
     * @return FragmentDefinitionNode[]
     */
    protected function getFragments()
    {
        return $this->fragments;
    }
    /**
     * @param callable[] $validators
     *
     * @return callable[]
     */
    protected function invokeIfNeeded(\Builderius\GraphQL\Validator\ValidationContext $context, array $validators)
    {
        // is disabled?
        if (!$this->isEnabled()) {
            return [];
        }
        $this->gatherFragmentDefinition($context);
        return $validators;
    }
    protected abstract function isEnabled();
    protected function gatherFragmentDefinition(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        // Gather all the fragment definition.
        // Importantly this does not include inline fragments.
        $definitions = $context->getDocument()->definitions;
        foreach ($definitions as $node) {
            if (!$node instanceof \Builderius\GraphQL\Language\AST\FragmentDefinitionNode) {
                continue;
            }
            $this->fragments[$node->name->value] = $node;
        }
    }
    /**
     * Given a selectionSet, adds all of the fields in that selection to
     * the passed in map of fields, and returns it at the end.
     *
     * Note: This is not the same as execution's collectFields because at static
     * time we do not know what object type will be used, so we unconditionally
     * spread in all fragments.
     *
     * @see \GraphQL\Validator\Rules\OverlappingFieldsCanBeMerged
     *
     * @param Type|null $parentType
     *
     * @return ArrayObject
     */
    protected function collectFieldASTsAndDefs(\Builderius\GraphQL\Validator\ValidationContext $context, $parentType, \Builderius\GraphQL\Language\AST\SelectionSetNode $selectionSet, ?\ArrayObject $visitedFragmentNames = null, ?\ArrayObject $astAndDefs = null)
    {
        $_visitedFragmentNames = $visitedFragmentNames ?? new \ArrayObject();
        $_astAndDefs = $astAndDefs ?? new \ArrayObject();
        foreach ($selectionSet->selections as $selection) {
            switch (\true) {
                case $selection instanceof \Builderius\GraphQL\Language\AST\FieldNode:
                    $fieldName = $selection->name->value;
                    $fieldDef = null;
                    if ($parentType && \method_exists($parentType, 'getFields')) {
                        $tmp = $parentType->getFields();
                        $schemaMetaFieldDef = \Builderius\GraphQL\Type\Introspection::schemaMetaFieldDef();
                        $typeMetaFieldDef = \Builderius\GraphQL\Type\Introspection::typeMetaFieldDef();
                        $typeNameMetaFieldDef = \Builderius\GraphQL\Type\Introspection::typeNameMetaFieldDef();
                        if ($fieldName === $schemaMetaFieldDef->name && $context->getSchema()->getQueryType() === $parentType) {
                            $fieldDef = $schemaMetaFieldDef;
                        } elseif ($fieldName === $typeMetaFieldDef->name && $context->getSchema()->getQueryType() === $parentType) {
                            $fieldDef = $typeMetaFieldDef;
                        } elseif ($fieldName === $typeNameMetaFieldDef->name) {
                            $fieldDef = $typeNameMetaFieldDef;
                        } elseif (isset($tmp[$fieldName])) {
                            $fieldDef = $tmp[$fieldName];
                        }
                    }
                    $responseName = $this->getFieldName($selection);
                    if (!isset($_astAndDefs[$responseName])) {
                        $_astAndDefs[$responseName] = new \ArrayObject();
                    }
                    // create field context
                    $_astAndDefs[$responseName][] = [$selection, $fieldDef];
                    break;
                case $selection instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode:
                    $_astAndDefs = $this->collectFieldASTsAndDefs($context, \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($context->getSchema(), $selection->typeCondition), $selection->selectionSet, $_visitedFragmentNames, $_astAndDefs);
                    break;
                case $selection instanceof \Builderius\GraphQL\Language\AST\FragmentSpreadNode:
                    $fragName = $selection->name->value;
                    if (!($_visitedFragmentNames[$fragName] ?? \false)) {
                        $_visitedFragmentNames[$fragName] = \true;
                        $fragment = $context->getFragment($fragName);
                        if ($fragment) {
                            $_astAndDefs = $this->collectFieldASTsAndDefs($context, \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($context->getSchema(), $fragment->typeCondition), $fragment->selectionSet, $_visitedFragmentNames, $_astAndDefs);
                        }
                    }
                    break;
            }
        }
        return $_astAndDefs;
    }
    protected function getFieldName(\Builderius\GraphQL\Language\AST\FieldNode $node)
    {
        $fieldName = $node->name->value;
        return $node->alias ? $node->alias->value : $fieldName;
    }
}
\class_alias(\Builderius\GraphQL\Validator\Rules\QuerySecurityRule::class, 'Builderius\\GraphQL\\Validator\\Rules\\AbstractQuerySecurity');
