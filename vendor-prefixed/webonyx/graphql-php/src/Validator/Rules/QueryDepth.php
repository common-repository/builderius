<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class QueryDepth extends \Builderius\GraphQL\Validator\Rules\QuerySecurityRule
{
    /** @var int */
    private $maxQueryDepth;
    public function __construct($maxQueryDepth)
    {
        $this->setMaxQueryDepth($maxQueryDepth);
    }
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return $this->invokeIfNeeded($context, [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => ['leave' => function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operationDefinition) use($context) : void {
            $maxDepth = $this->fieldDepth($operationDefinition);
            if ($maxDepth <= $this->getMaxQueryDepth()) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::maxQueryDepthErrorMessage($this->getMaxQueryDepth(), $maxDepth)));
        }]]);
    }
    private function fieldDepth($node, $depth = 0, $maxDepth = 0)
    {
        if (isset($node->selectionSet) && $node->selectionSet instanceof \Builderius\GraphQL\Language\AST\SelectionSetNode) {
            foreach ($node->selectionSet->selections as $childNode) {
                $maxDepth = $this->nodeDepth($childNode, $depth, $maxDepth);
            }
        }
        return $maxDepth;
    }
    private function nodeDepth(\Builderius\GraphQL\Language\AST\Node $node, $depth = 0, $maxDepth = 0)
    {
        switch (\true) {
            case $node instanceof \Builderius\GraphQL\Language\AST\FieldNode:
                // node has children?
                if ($node->selectionSet !== null) {
                    // update maxDepth if needed
                    if ($depth > $maxDepth) {
                        $maxDepth = $depth;
                    }
                    $maxDepth = $this->fieldDepth($node, $depth + 1, $maxDepth);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode:
                // node has children?
                if ($node->selectionSet !== null) {
                    $maxDepth = $this->fieldDepth($node, $depth, $maxDepth);
                }
                break;
            case $node instanceof \Builderius\GraphQL\Language\AST\FragmentSpreadNode:
                $fragment = $this->getFragment($node);
                if ($fragment !== null) {
                    $maxDepth = $this->fieldDepth($fragment, $depth, $maxDepth);
                }
                break;
        }
        return $maxDepth;
    }
    public function getMaxQueryDepth()
    {
        return $this->maxQueryDepth;
    }
    /**
     * Set max query depth. If equal to 0 no check is done. Must be greater or equal to 0.
     */
    public function setMaxQueryDepth($maxQueryDepth)
    {
        $this->checkIfGreaterOrEqualToZero('maxQueryDepth', $maxQueryDepth);
        $this->maxQueryDepth = (int) $maxQueryDepth;
    }
    public static function maxQueryDepthErrorMessage($max, $count)
    {
        return \sprintf('Max query depth should be %d but got %d.', $max, $count);
    }
    protected function isEnabled()
    {
        return $this->getMaxQueryDepth() !== self::DISABLED;
    }
}
