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
use Builderius\Twig\Node\CheckSecurityCallNode;
use Builderius\Twig\Node\CheckSecurityNode;
use Builderius\Twig\Node\CheckToStringNode;
use Builderius\Twig\Node\Expression\Binary\ConcatBinary;
use Builderius\Twig\Node\Expression\Binary\RangeBinary;
use Builderius\Twig\Node\Expression\FilterExpression;
use Builderius\Twig\Node\Expression\FunctionExpression;
use Builderius\Twig\Node\Expression\GetAttrExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\ModuleNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\PrintNode;
use Builderius\Twig\Node\SetNode;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class SandboxNodeVisitor implements \Builderius\Twig\NodeVisitor\NodeVisitorInterface
{
    private $inAModule = \false;
    private $tags;
    private $filters;
    private $functions;
    private $needsToStringWrap = \false;
    public function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->inAModule = \true;
            $this->tags = [];
            $this->filters = [];
            $this->functions = [];
            return $node;
        } elseif ($this->inAModule) {
            // look for tags
            if ($node->getNodeTag() && !isset($this->tags[$node->getNodeTag()])) {
                $this->tags[$node->getNodeTag()] = $node;
            }
            // look for filters
            if ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression && !isset($this->filters[$node->getNode('filter')->getAttribute('value')])) {
                $this->filters[$node->getNode('filter')->getAttribute('value')] = $node;
            }
            // look for functions
            if ($node instanceof \Builderius\Twig\Node\Expression\FunctionExpression && !isset($this->functions[$node->getAttribute('name')])) {
                $this->functions[$node->getAttribute('name')] = $node;
            }
            // the .. operator is equivalent to the range() function
            if ($node instanceof \Builderius\Twig\Node\Expression\Binary\RangeBinary && !isset($this->functions['range'])) {
                $this->functions['range'] = $node;
            }
            if ($node instanceof \Builderius\Twig\Node\PrintNode) {
                $this->needsToStringWrap = \true;
                $this->wrapNode($node, 'expr');
            }
            if ($node instanceof \Builderius\Twig\Node\SetNode && !$node->getAttribute('capture')) {
                $this->needsToStringWrap = \true;
            }
            // wrap outer nodes that can implicitly call __toString()
            if ($this->needsToStringWrap) {
                if ($node instanceof \Builderius\Twig\Node\Expression\Binary\ConcatBinary) {
                    $this->wrapNode($node, 'left');
                    $this->wrapNode($node, 'right');
                }
                if ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression) {
                    $this->wrapNode($node, 'node');
                    $this->wrapArrayNode($node, 'arguments');
                }
                if ($node instanceof \Builderius\Twig\Node\Expression\FunctionExpression) {
                    $this->wrapArrayNode($node, 'arguments');
                }
            }
        }
        return $node;
    }
    public function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->inAModule = \false;
            $node->setNode('constructor_end', new \Builderius\Twig\Node\Node([new \Builderius\Twig\Node\CheckSecurityCallNode(), $node->getNode('constructor_end')]));
            $node->setNode('class_end', new \Builderius\Twig\Node\Node([new \Builderius\Twig\Node\CheckSecurityNode($this->filters, $this->tags, $this->functions), $node->getNode('class_end')]));
        } elseif ($this->inAModule) {
            if ($node instanceof \Builderius\Twig\Node\PrintNode || $node instanceof \Builderius\Twig\Node\SetNode) {
                $this->needsToStringWrap = \false;
            }
        }
        return $node;
    }
    private function wrapNode(\Builderius\Twig\Node\Node $node, string $name) : void
    {
        $expr = $node->getNode($name);
        if ($expr instanceof \Builderius\Twig\Node\Expression\NameExpression || $expr instanceof \Builderius\Twig\Node\Expression\GetAttrExpression) {
            $node->setNode($name, new \Builderius\Twig\Node\CheckToStringNode($expr));
        }
    }
    private function wrapArrayNode(\Builderius\Twig\Node\Node $node, string $name) : void
    {
        $args = $node->getNode($name);
        foreach ($args as $name => $_) {
            $this->wrapNode($args, $name);
        }
    }
    public function getPriority() : int
    {
        return 0;
    }
}
