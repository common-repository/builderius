<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\Visitor;
use Builderius\GraphQL\Language\VisitorOperation;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ValidationContext;
use function array_pop;
use function array_slice;
use function count;
use function implode;
use function sprintf;
class NoFragmentCycles extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var bool[] */
    public $visitedFrags;
    /** @var FragmentSpreadNode[] */
    public $spreadPath;
    /** @var (int|null)[] */
    public $spreadPathIndexByName;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        // Tracks already visited fragments to maintain O(N) and to ensure that cycles
        // are not redundantly reported.
        $this->visitedFrags = [];
        // Array of AST nodes used to produce meaningful errors
        $this->spreadPath = [];
        // Position in the spread path
        $this->spreadPathIndexByName = [];
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => static function () : VisitorOperation {
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => function (\Builderius\GraphQL\Language\AST\FragmentDefinitionNode $node) use($context) : VisitorOperation {
            $this->detectCycleRecursive($node, $context);
            return \Builderius\GraphQL\Language\Visitor::skipNode();
        }];
    }
    private function detectCycleRecursive(\Builderius\GraphQL\Language\AST\FragmentDefinitionNode $fragment, \Builderius\GraphQL\Validator\ValidationContext $context)
    {
        if (isset($this->visitedFrags[$fragment->name->value])) {
            return;
        }
        $fragmentName = $fragment->name->value;
        $this->visitedFrags[$fragmentName] = \true;
        $spreadNodes = $context->getFragmentSpreads($fragment);
        if (\count($spreadNodes) === 0) {
            return;
        }
        $this->spreadPathIndexByName[$fragmentName] = \count($this->spreadPath);
        for ($i = 0; $i < \count($spreadNodes); $i++) {
            $spreadNode = $spreadNodes[$i];
            $spreadName = $spreadNode->name->value;
            $cycleIndex = $this->spreadPathIndexByName[$spreadName] ?? null;
            $this->spreadPath[] = $spreadNode;
            if ($cycleIndex === null) {
                $spreadFragment = $context->getFragment($spreadName);
                if ($spreadFragment) {
                    $this->detectCycleRecursive($spreadFragment, $context);
                }
            } else {
                $cyclePath = \array_slice($this->spreadPath, $cycleIndex);
                $fragmentNames = \Builderius\GraphQL\Utils\Utils::map(\array_slice($cyclePath, 0, -1), static function ($s) {
                    return $s->name->value;
                });
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::cycleErrorMessage($spreadName, $fragmentNames), $cyclePath));
            }
            \array_pop($this->spreadPath);
        }
        $this->spreadPathIndexByName[$fragmentName] = null;
    }
    /**
     * @param string[] $spreadNames
     */
    public static function cycleErrorMessage($fragName, array $spreadNames = [])
    {
        return \sprintf('Cannot spread fragment "%s" within itself%s.', $fragName, \count($spreadNames) > 0 ? ' via ' . \implode(', ', $spreadNames) : '');
    }
}
