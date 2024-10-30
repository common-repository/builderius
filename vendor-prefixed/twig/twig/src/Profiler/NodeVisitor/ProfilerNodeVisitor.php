<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Profiler\NodeVisitor;

use Builderius\Twig\Environment;
use Builderius\Twig\Node\BlockNode;
use Builderius\Twig\Node\BodyNode;
use Builderius\Twig\Node\MacroNode;
use Builderius\Twig\Node\ModuleNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\NodeVisitor\NodeVisitorInterface;
use Builderius\Twig\Profiler\Node\EnterProfileNode;
use Builderius\Twig\Profiler\Node\LeaveProfileNode;
use Builderius\Twig\Profiler\Profile;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ProfilerNodeVisitor implements \Builderius\Twig\NodeVisitor\NodeVisitorInterface
{
    private $extensionName;
    private $varName;
    public function __construct(string $extensionName)
    {
        $this->extensionName = $extensionName;
        $this->varName = \sprintf('__internal_%s', \hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $extensionName));
    }
    public function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        return $node;
    }
    public function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            $node->setNode('display_start', new \Builderius\Twig\Node\Node([new \Builderius\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \Builderius\Twig\Profiler\Profile::TEMPLATE, $node->getTemplateName(), $this->varName), $node->getNode('display_start')]));
            $node->setNode('display_end', new \Builderius\Twig\Node\Node([new \Builderius\Twig\Profiler\Node\LeaveProfileNode($this->varName), $node->getNode('display_end')]));
        } elseif ($node instanceof \Builderius\Twig\Node\BlockNode) {
            $node->setNode('body', new \Builderius\Twig\Node\BodyNode([new \Builderius\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \Builderius\Twig\Profiler\Profile::BLOCK, $node->getAttribute('name'), $this->varName), $node->getNode('body'), new \Builderius\Twig\Profiler\Node\LeaveProfileNode($this->varName)]));
        } elseif ($node instanceof \Builderius\Twig\Node\MacroNode) {
            $node->setNode('body', new \Builderius\Twig\Node\BodyNode([new \Builderius\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \Builderius\Twig\Profiler\Profile::MACRO, $node->getAttribute('name'), $this->varName), $node->getNode('body'), new \Builderius\Twig\Profiler\Node\LeaveProfileNode($this->varName)]));
        }
        return $node;
    }
    public function getPriority() : int
    {
        return 0;
    }
}
