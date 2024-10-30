<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\NodeVisitor;

use Builderius\Twig\Environment;
use Builderius\Twig\Node\BlockReferenceNode;
use Builderius\Twig\Node\Expression\BlockReferenceExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FilterExpression;
use Builderius\Twig\Node\Expression\FunctionExpression;
use Builderius\Twig\Node\Expression\GetAttrExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\Expression\ParentExpression;
use Builderius\Twig\Node\ForNode;
use Builderius\Twig\Node\IncludeNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\PrintNode;
/**
 * Tries to optimize the AST.
 *
 * This visitor is always the last registered one.
 *
 * You can configure which optimizations you want to activate via the
 * optimizer mode.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class OptimizerNodeVisitor implements \Builderius\Twig\NodeVisitor\NodeVisitorInterface
{
    public const OPTIMIZE_ALL = -1;
    public const OPTIMIZE_NONE = 0;
    public const OPTIMIZE_FOR = 2;
    public const OPTIMIZE_RAW_FILTER = 4;
    private $loops = [];
    private $loopsTargets = [];
    private $optimizers;
    /**
     * @param int $optimizers The optimizer mode
     */
    public function __construct(int $optimizers = -1)
    {
        if ($optimizers > (self::OPTIMIZE_FOR | self::OPTIMIZE_RAW_FILTER)) {
            throw new \InvalidArgumentException(\sprintf('Optimizer mode "%s" is not valid.', $optimizers));
        }
        $this->optimizers = $optimizers;
    }
    public function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
            $this->enterOptimizeFor($node, $env);
        }
        return $node;
    }
    public function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
            $this->leaveOptimizeFor($node, $env);
        }
        if (self::OPTIMIZE_RAW_FILTER === (self::OPTIMIZE_RAW_FILTER & $this->optimizers)) {
            $node = $this->optimizeRawFilter($node, $env);
        }
        $node = $this->optimizePrintNode($node, $env);
        return $node;
    }
    /**
     * Optimizes print nodes.
     *
     * It replaces:
     *
     *   * "echo $this->render(Parent)Block()" with "$this->display(Parent)Block()"
     */
    private function optimizePrintNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if (!$node instanceof \Builderius\Twig\Node\PrintNode) {
            return $node;
        }
        $exprNode = $node->getNode('expr');
        if ($exprNode instanceof \Builderius\Twig\Node\Expression\BlockReferenceExpression || $exprNode instanceof \Builderius\Twig\Node\Expression\ParentExpression) {
            $exprNode->setAttribute('output', \true);
            return $exprNode;
        }
        return $node;
    }
    /**
     * Removes "raw" filters.
     */
    private function optimizeRawFilter(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression && 'raw' == $node->getNode('filter')->getAttribute('value')) {
            return $node->getNode('node');
        }
        return $node;
    }
    /**
     * Optimizes "for" tag by removing the "loop" variable creation whenever possible.
     */
    private function enterOptimizeFor(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : void
    {
        if ($node instanceof \Builderius\Twig\Node\ForNode) {
            // disable the loop variable by default
            $node->setAttribute('with_loop', \false);
            \array_unshift($this->loops, $node);
            \array_unshift($this->loopsTargets, $node->getNode('value_target')->getAttribute('name'));
            \array_unshift($this->loopsTargets, $node->getNode('key_target')->getAttribute('name'));
        } elseif (!$this->loops) {
            // we are outside a loop
            return;
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\NameExpression && 'loop' === $node->getAttribute('name')) {
            $node->setAttribute('always_defined', \true);
            $this->addLoopToCurrent();
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\NameExpression && \in_array($node->getAttribute('name'), $this->loopsTargets)) {
            $node->setAttribute('always_defined', \true);
        } elseif ($node instanceof \Builderius\Twig\Node\BlockReferenceNode || $node instanceof \Builderius\Twig\Node\Expression\BlockReferenceExpression) {
            $this->addLoopToCurrent();
        } elseif ($node instanceof \Builderius\Twig\Node\IncludeNode && !$node->getAttribute('only')) {
            $this->addLoopToAll();
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\FunctionExpression && 'include' === $node->getAttribute('name') && (!$node->getNode('arguments')->hasNode('with_context') || \false !== $node->getNode('arguments')->getNode('with_context')->getAttribute('value'))) {
            $this->addLoopToAll();
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\GetAttrExpression && (!$node->getNode('attribute') instanceof \Builderius\Twig\Node\Expression\ConstantExpression || 'parent' === $node->getNode('attribute')->getAttribute('value')) && (\true === $this->loops[0]->getAttribute('with_loop') || $node->getNode('node') instanceof \Builderius\Twig\Node\Expression\NameExpression && 'loop' === $node->getNode('node')->getAttribute('name'))) {
            $this->addLoopToAll();
        }
    }
    /**
     * Optimizes "for" tag by removing the "loop" variable creation whenever possible.
     */
    private function leaveOptimizeFor(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : void
    {
        if ($node instanceof \Builderius\Twig\Node\ForNode) {
            \array_shift($this->loops);
            \array_shift($this->loopsTargets);
            \array_shift($this->loopsTargets);
        }
    }
    private function addLoopToCurrent() : void
    {
        $this->loops[0]->setAttribute('with_loop', \true);
    }
    private function addLoopToAll() : void
    {
        foreach ($this->loops as $loop) {
            $loop->setAttribute('with_loop', \true);
        }
    }
    public function getPriority() : int
    {
        return 255;
    }
}
