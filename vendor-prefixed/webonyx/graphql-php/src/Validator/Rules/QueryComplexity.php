<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use ArrayObject;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Executor\Values;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\FieldDefinition;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_map;
use function call_user_func_array;
use function count;
use function implode;
use function method_exists;
use function sprintf;
class QueryComplexity extends \Builderius\GraphQL\Validator\Rules\QuerySecurityRule
{
    /** @var int */
    private $maxQueryComplexity;
    /** @var mixed[]|null */
    private $rawVariableValues = [];
    /** @var ArrayObject */
    private $variableDefs;
    /** @var ArrayObject */
    private $fieldNodeAndDefs;
    /** @var ValidationContext */
    private $context;
    /** @var int */
    private $complexity;
    public function __construct($maxQueryComplexity)
    {
        $this->setMaxQueryComplexity($maxQueryComplexity);
    }
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $this->context = $context;
        $this->variableDefs = new \ArrayObject();
        $this->fieldNodeAndDefs = new \ArrayObject();
        $this->complexity = 0;
        return $this->invokeIfNeeded($context, [\Builderius\GraphQL\Language\AST\NodeKind::SELECTION_SET => function (\Builderius\GraphQL\Language\AST\SelectionSetNode $selectionSet) use($context) : void {
            $this->fieldNodeAndDefs = $this->collectFieldASTsAndDefs($context, $context->getParentType(), $selectionSet, null, $this->fieldNodeAndDefs);
        }, \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => function ($def) : VisitorOperation {
            $this->variableDefs[] = $def;
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }, \Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => ['leave' => function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operationDefinition) use($context, &$complexity) : void {
            $errors = $context->getErrors();
            if (\count($errors) > 0) {
                return;
            }
            $this->complexity = $this->fieldComplexity($operationDefinition, $complexity);
            if ($this->getQueryComplexity() <= $this->getMaxQueryComplexity()) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::maxQueryComplexityErrorMessage($this->getMaxQueryComplexity(), $this->getQueryComplexity())));
        }]]);
    }
    private function fieldComplexity($node, $complexity = 0)
    {
        if (isset($node->selectionSet) && $node->selectionSet instanceof \Builderius\GraphQL\Language\AST\SelectionSetNode) {
            foreach ($node->selectionSet->selections as $childNode) {
                $complexity = $this->nodeComplexity($childNode, $complexity);
            }
        }
        return $complexity;
    }
    private function nodeComplexity(\Builderius\GraphQL\Language\AST\Node $node, $complexity = 0)
    {
        switch (\true) {
            case $node instanceof \Builderius\GraphQL\Language\AST\FieldNode:
                // default values
                $args = [];
                $complexityFn = \Builderius\GraphQL\Type\Definition\FieldDefinition::DEFAULT_COMPLEXITY_FN;
                // calculate children complexity if needed
                $childrenComplexity = 0;
                // node has children?
                if (isset($node->selectionSet)) {
                    $childrenComplexity = $this->fieldComplexity($node);
                }
                $astFieldInfo = $this->astFieldInfo($node);
                $fieldDef = $astFieldInfo[1];
                if ($fieldDef instanceof \Builderius\GraphQL\Type\Definition\FieldDefinition) {
                    if ($this->directiveExcludesField($node)) {
                        break;
                    }
                    $args = $this->buildFieldArguments($node);
                    //get complexity fn using fieldDef complexity
                    if (\method_exists($fieldDef, 'getComplexityFn')) {
                        $complexityFn = $fieldDef->getComplexityFn();
                    }
                }
                $complexity += $complexityFn($childrenComplexity, $args);
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode:
                // node has children?
                if (isset($node->selectionSet)) {
                    $complexity = $this->fieldComplexity($node, $complexity);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\FragmentSpreadNode:
                $fragment = $this->getFragment($node);
                if ($fragment !== null) {
                    $complexity = $this->fieldComplexity($fragment, $complexity);
                }
                break;
        }
        return $complexity;
    }
    private function astFieldInfo(\Builderius\GraphQL\Language\AST\FieldNode $field)
    {
        $fieldName = $this->getFieldName($field);
        $astFieldInfo = [null, null];
        if (isset($this->fieldNodeAndDefs[$fieldName])) {
            foreach ($this->fieldNodeAndDefs[$fieldName] as $astAndDef) {
                if ($astAndDef[0] === $field) {
                    $astFieldInfo = $astAndDef;
                    break;
                }
            }
        }
        return $astFieldInfo;
    }
    private function directiveExcludesField(\Builderius\GraphQL\Language\AST\FieldNode $node)
    {
        foreach ($node->directives as $directiveNode) {
            if ($directiveNode->name->value === 'deprecated') {
                return \false;
            }
            [$errors, $variableValues] = \Builderius\GraphQL\Executor\Values::getVariableValues($this->context->getSchema(), $this->variableDefs, $this->getRawVariableValues());
            if (\count($errors ?? []) > 0) {
                throw new \Builderius\GraphQL\Error\Error(\implode("\n\n", \array_map(static function ($error) {
                    return $error->getMessage();
                }, $errors)));
            }
            if ($directiveNode->name->value === 'include') {
                $directive = \Builderius\GraphQL\Type\Definition\Directive::includeDirective();
                /** @var bool $directiveArgsIf */
                $directiveArgsIf = \Builderius\GraphQL\Executor\Values::getArgumentValues($directive, $directiveNode, $variableValues)['if'];
                return !$directiveArgsIf;
            }
            if ($directiveNode->name->value === \Builderius\GraphQL\Type\Definition\Directive::SKIP_NAME) {
                $directive = \Builderius\GraphQL\Type\Definition\Directive::skipDirective();
                /** @var bool $directiveArgsIf */
                $directiveArgsIf = \Builderius\GraphQL\Executor\Values::getArgumentValues($directive, $directiveNode, $variableValues)['if'];
                return $directiveArgsIf;
            }
        }
        return \false;
    }
    public function getRawVariableValues()
    {
        return $this->rawVariableValues;
    }
    /**
     * @param mixed[]|null $rawVariableValues
     */
    public function setRawVariableValues(?array $rawVariableValues = null)
    {
        $this->rawVariableValues = $rawVariableValues ?? [];
    }
    private function buildFieldArguments(\Builderius\GraphQL\Language\AST\FieldNode $node)
    {
        $rawVariableValues = $this->getRawVariableValues();
        $astFieldInfo = $this->astFieldInfo($node);
        $fieldDef = $astFieldInfo[1];
        $args = [];
        if ($fieldDef instanceof \Builderius\GraphQL\Type\Definition\FieldDefinition) {
            [$errors, $variableValues] = \Builderius\GraphQL\Executor\Values::getVariableValues($this->context->getSchema(), $this->variableDefs, $rawVariableValues);
            if (\count($errors ?? []) > 0) {
                throw new \Builderius\GraphQL\Error\Error(\implode("\n\n", \array_map(static function ($error) {
                    return $error->getMessage();
                }, $errors)));
            }
            $args = \Builderius\GraphQL\Executor\Values::getArgumentValues($fieldDef, $node, $variableValues);
        }
        return $args;
    }
    public function getQueryComplexity()
    {
        return $this->complexity;
    }
    public function getMaxQueryComplexity()
    {
        return $this->maxQueryComplexity;
    }
    /**
     * Set max query complexity. If equal to 0 no check is done. Must be greater or equal to 0.
     */
    public function setMaxQueryComplexity($maxQueryComplexity)
    {
        $this->checkIfGreaterOrEqualToZero('maxQueryComplexity', $maxQueryComplexity);
        $this->maxQueryComplexity = (int) $maxQueryComplexity;
    }
    public static function maxQueryComplexityErrorMessage($max, $count)
    {
        return \sprintf('Max query complexity should be %d but got %d.', $max, $count);
    }
    protected function isEnabled()
    {
        return $this->getMaxQueryComplexity() !== self::DISABLED;
    }
}
