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
use Builderius\Twig\Extension\EscaperExtension;
use Builderius\Twig\Node\AutoEscapeNode;
use Builderius\Twig\Node\BlockNode;
use Builderius\Twig\Node\BlockReferenceNode;
use Builderius\Twig\Node\DoNode;
use Builderius\Twig\Node\Expression\ConditionalExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FilterExpression;
use Builderius\Twig\Node\Expression\InlinePrint;
use Builderius\Twig\Node\ImportNode;
use Builderius\Twig\Node\ModuleNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\PrintNode;
use Builderius\Twig\NodeTraverser;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class EscaperNodeVisitor implements \Builderius\Twig\NodeVisitor\NodeVisitorInterface
{
    private $statusStack = [];
    private $blocks = [];
    private $safeAnalysis;
    private $traverser;
    private $defaultStrategy = \false;
    private $safeVars = [];
    public function __construct()
    {
        $this->safeAnalysis = new \Builderius\Twig\NodeVisitor\SafeAnalysisNodeVisitor();
    }
    public function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            if ($env->hasExtension(\Builderius\Twig\Extension\EscaperExtension::class) && ($defaultStrategy = $env->getExtension(\Builderius\Twig\Extension\EscaperExtension::class)->getDefaultStrategy($node->getTemplateName()))) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof \Builderius\Twig\Node\AutoEscapeNode) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof \Builderius\Twig\Node\BlockNode) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ? $this->blocks[$node->getAttribute('name')] : $this->needEscaping($env);
        } elseif ($node instanceof \Builderius\Twig\Node\ImportNode) {
            $this->safeVars[] = $node->getNode('var')->getAttribute('name');
        }
        return $node;
    }
    public function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->defaultStrategy = \false;
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof \Builderius\Twig\Node\PrintNode && \false !== ($type = $this->needEscaping($env))) {
            $expression = $node->getNode('expr');
            if ($expression instanceof \Builderius\Twig\Node\Expression\ConditionalExpression && $this->shouldUnwrapConditional($expression, $env, $type)) {
                return new \Builderius\Twig\Node\DoNode($this->unwrapConditional($expression, $env, $type), $expression->getTemplateLine());
            }
            return $this->escapePrintNode($node, $env, $type);
        }
        if ($node instanceof \Builderius\Twig\Node\AutoEscapeNode || $node instanceof \Builderius\Twig\Node\BlockNode) {
            \array_pop($this->statusStack);
        } elseif ($node instanceof \Builderius\Twig\Node\BlockReferenceNode) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping($env);
        }
        return $node;
    }
    private function shouldUnwrapConditional(\Builderius\Twig\Node\Expression\ConditionalExpression $expression, \Builderius\Twig\Environment $env, string $type) : bool
    {
        $expr2Safe = $this->isSafeFor($type, $expression->getNode('expr2'), $env);
        $expr3Safe = $this->isSafeFor($type, $expression->getNode('expr3'), $env);
        return $expr2Safe !== $expr3Safe;
    }
    private function unwrapConditional(\Builderius\Twig\Node\Expression\ConditionalExpression $expression, \Builderius\Twig\Environment $env, string $type) : \Builderius\Twig\Node\Expression\ConditionalExpression
    {
        // convert "echo a ? b : c" to "a ? echo b : echo c" recursively
        $expr2 = $expression->getNode('expr2');
        if ($expr2 instanceof \Builderius\Twig\Node\Expression\ConditionalExpression && $this->shouldUnwrapConditional($expr2, $env, $type)) {
            $expr2 = $this->unwrapConditional($expr2, $env, $type);
        } else {
            $expr2 = $this->escapeInlinePrintNode(new \Builderius\Twig\Node\Expression\InlinePrint($expr2, $expr2->getTemplateLine()), $env, $type);
        }
        $expr3 = $expression->getNode('expr3');
        if ($expr3 instanceof \Builderius\Twig\Node\Expression\ConditionalExpression && $this->shouldUnwrapConditional($expr3, $env, $type)) {
            $expr3 = $this->unwrapConditional($expr3, $env, $type);
        } else {
            $expr3 = $this->escapeInlinePrintNode(new \Builderius\Twig\Node\Expression\InlinePrint($expr3, $expr3->getTemplateLine()), $env, $type);
        }
        return new \Builderius\Twig\Node\Expression\ConditionalExpression($expression->getNode('expr1'), $expr2, $expr3, $expression->getTemplateLine());
    }
    private function escapeInlinePrintNode(\Builderius\Twig\Node\Expression\InlinePrint $node, \Builderius\Twig\Environment $env, string $type) : \Builderius\Twig\Node\Node
    {
        $expression = $node->getNode('node');
        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }
        return new \Builderius\Twig\Node\Expression\InlinePrint($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
    }
    private function escapePrintNode(\Builderius\Twig\Node\PrintNode $node, \Builderius\Twig\Environment $env, string $type) : \Builderius\Twig\Node\Node
    {
        if (\false === $type) {
            return $node;
        }
        $expression = $node->getNode('expr');
        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }
        $class = \get_class($node);
        return new $class($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
    }
    private function preEscapeFilterNode(\Builderius\Twig\Node\Expression\FilterExpression $filter, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Expression\FilterExpression
    {
        $name = $filter->getNode('filter')->getAttribute('value');
        $type = $env->getFilter($name)->getPreEscape();
        if (null === $type) {
            return $filter;
        }
        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }
        $filter->setNode('node', $this->getEscaperFilter($type, $node));
        return $filter;
    }
    private function isSafeFor(string $type, \Builderius\Twig\Node\Node $expression, \Builderius\Twig\Environment $env) : bool
    {
        $safe = $this->safeAnalysis->getSafe($expression);
        if (null === $safe) {
            if (null === $this->traverser) {
                $this->traverser = new \Builderius\Twig\NodeTraverser($env, [$this->safeAnalysis]);
            }
            $this->safeAnalysis->setSafeVars($this->safeVars);
            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }
        return \in_array($type, $safe) || \in_array('all', $safe);
    }
    private function needEscaping(\Builderius\Twig\Environment $env)
    {
        if (\count($this->statusStack)) {
            return $this->statusStack[\count($this->statusStack) - 1];
        }
        return $this->defaultStrategy ? $this->defaultStrategy : \false;
    }
    private function getEscaperFilter(string $type, \Builderius\Twig\Node\Node $node) : \Builderius\Twig\Node\Expression\FilterExpression
    {
        $line = $node->getTemplateLine();
        $name = new \Builderius\Twig\Node\Expression\ConstantExpression('escape', $line);
        $args = new \Builderius\Twig\Node\Node([new \Builderius\Twig\Node\Expression\ConstantExpression($type, $line), new \Builderius\Twig\Node\Expression\ConstantExpression(null, $line), new \Builderius\Twig\Node\Expression\ConstantExpression(\true, $line)]);
        return new \Builderius\Twig\Node\Expression\FilterExpression($node, $name, $args, $line);
    }
    public function getPriority() : int
    {
        return 0;
    }
}
