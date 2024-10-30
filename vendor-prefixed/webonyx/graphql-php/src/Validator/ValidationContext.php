<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\HasSelectionSet;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\AST\VariableNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Type\Definition\CompositeType;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\FieldDefinition;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\OutputType;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\TypeInfo;
use SplObjectStorage;
use function array_merge;
use function array_pop;
use function count;
/**
 * An instance of this class is passed as the "this" context to all validators,
 * allowing access to commonly useful contextual information from within a
 * validation rule.
 */
class ValidationContext extends \Builderius\GraphQL\Validator\ASTValidationContext
{
    /** @var TypeInfo */
    private $typeInfo;
    /** @var FragmentDefinitionNode[] */
    private $fragments;
    /** @var SplObjectStorage */
    private $fragmentSpreads;
    /** @var SplObjectStorage */
    private $recursivelyReferencedFragments;
    /** @var SplObjectStorage */
    private $variableUsages;
    /** @var SplObjectStorage */
    private $recursiveVariableUsages;
    public function __construct(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $ast, \Builderius\GraphQL\Utils\TypeInfo $typeInfo)
    {
        parent::__construct($ast, $schema);
        $this->typeInfo = $typeInfo;
        $this->fragmentSpreads = new \SplObjectStorage();
        $this->recursivelyReferencedFragments = new \SplObjectStorage();
        $this->variableUsages = new \SplObjectStorage();
        $this->recursiveVariableUsages = new \SplObjectStorage();
    }
    /**
     * @return mixed[][] List of ['node' => VariableNode, 'type' => ?InputObjectType]
     */
    public function getRecursiveVariableUsages(\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operation)
    {
        $usages = $this->recursiveVariableUsages[$operation] ?? null;
        if ($usages === null) {
            $usages = $this->getVariableUsages($operation);
            $fragments = $this->getRecursivelyReferencedFragments($operation);
            $allUsages = [$usages];
            foreach ($fragments as $fragment) {
                $allUsages[] = $this->getVariableUsages($fragment);
            }
            $usages = \array_merge(...$allUsages);
            $this->recursiveVariableUsages[$operation] = $usages;
        }
        return $usages;
    }
    /**
     * @return mixed[][] List of ['node' => VariableNode, 'type' => ?InputObjectType]
     */
    private function getVariableUsages(\Builderius\GraphQL\Language\AST\HasSelectionSet $node)
    {
        $usages = $this->variableUsages[$node] ?? null;
        if ($usages === null) {
            $newUsages = [];
            $typeInfo = new \Builderius\GraphQL\Utils\TypeInfo($this->schema);
            \Builderius\GraphQL\Language\Visitor::visit($node, \Builderius\GraphQL\Language\Visitor::visitWithTypeInfo($typeInfo, [\Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => static function () : bool {
                return \false;
            }, \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE => static function (\Builderius\GraphQL\Language\AST\VariableNode $variable) use(&$newUsages, $typeInfo) : void {
                $newUsages[] = ['node' => $variable, 'type' => $typeInfo->getInputType(), 'defaultValue' => $typeInfo->getDefaultValue()];
            }]));
            $usages = $newUsages;
            $this->variableUsages[$node] = $usages;
        }
        return $usages;
    }
    /**
     * @return FragmentDefinitionNode[]
     */
    public function getRecursivelyReferencedFragments(\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operation)
    {
        $fragments = $this->recursivelyReferencedFragments[$operation] ?? null;
        if ($fragments === null) {
            $fragments = [];
            $collectedNames = [];
            $nodesToVisit = [$operation];
            while (\count($nodesToVisit) > 0) {
                $node = \array_pop($nodesToVisit);
                $spreads = $this->getFragmentSpreads($node);
                foreach ($spreads as $spread) {
                    $fragName = $spread->name->value;
                    if ($collectedNames[$fragName] ?? \false) {
                        continue;
                    }
                    $collectedNames[$fragName] = \true;
                    $fragment = $this->getFragment($fragName);
                    if (!$fragment) {
                        continue;
                    }
                    $fragments[] = $fragment;
                    $nodesToVisit[] = $fragment;
                }
            }
            $this->recursivelyReferencedFragments[$operation] = $fragments;
        }
        return $fragments;
    }
    /**
     * @param OperationDefinitionNode|FragmentDefinitionNode $node
     *
     * @return FragmentSpreadNode[]
     */
    public function getFragmentSpreads(\Builderius\GraphQL\Language\AST\HasSelectionSet $node) : array
    {
        $spreads = $this->fragmentSpreads[$node] ?? null;
        if ($spreads === null) {
            $spreads = [];
            /** @var SelectionSetNode[] $setsToVisit */
            $setsToVisit = [$node->selectionSet];
            while (\count($setsToVisit) > 0) {
                $set = \array_pop($setsToVisit);
                for ($i = 0, $selectionCount = \count($set->selections); $i < $selectionCount; $i++) {
                    $selection = $set->selections[$i];
                    if ($selection instanceof \Builderius\GraphQL\Language\AST\FragmentSpreadNode) {
                        $spreads[] = $selection;
                    } elseif ($selection instanceof \Builderius\GraphQL\Language\AST\FieldNode || $selection instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode) {
                        if ($selection->selectionSet) {
                            $setsToVisit[] = $selection->selectionSet;
                        }
                    } else {
                        throw \Builderius\GraphQL\Error\InvariantViolation::shouldNotHappen();
                    }
                }
            }
            $this->fragmentSpreads[$node] = $spreads;
        }
        return $spreads;
    }
    /**
     * @param string $name
     *
     * @return FragmentDefinitionNode|null
     */
    public function getFragment($name)
    {
        $fragments = $this->fragments;
        if (!$fragments) {
            $fragments = [];
            foreach ($this->getDocument()->definitions as $statement) {
                if (!$statement instanceof \Builderius\GraphQL\Language\AST\FragmentDefinitionNode) {
                    continue;
                }
                $fragments[$statement->name->value] = $statement;
            }
            $this->fragments = $fragments;
        }
        return $fragments[$name] ?? null;
    }
    public function getType() : ?\Builderius\GraphQL\Type\Definition\OutputType
    {
        return $this->typeInfo->getType();
    }
    /**
     * @return (CompositeType & Type) | null
     */
    public function getParentType() : ?\Builderius\GraphQL\Type\Definition\CompositeType
    {
        return $this->typeInfo->getParentType();
    }
    /**
     * @return (Type & InputType) | null
     */
    public function getInputType() : ?\Builderius\GraphQL\Type\Definition\InputType
    {
        return $this->typeInfo->getInputType();
    }
    /**
     * @return (Type&InputType)|null
     */
    public function getParentInputType() : ?\Builderius\GraphQL\Type\Definition\InputType
    {
        return $this->typeInfo->getParentInputType();
    }
    /**
     * @return FieldDefinition
     */
    public function getFieldDef()
    {
        return $this->typeInfo->getFieldDef();
    }
    public function getDirective()
    {
        return $this->typeInfo->getDirective();
    }
    public function getArgument()
    {
        return $this->typeInfo->getArgument();
    }
}
