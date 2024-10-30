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
use Builderius\Twig\Node\Node;
/**
 * Used to make node visitors compatible with Twig 1.x and 2.x.
 *
 * To be removed in Twig 3.1.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractNodeVisitor implements \Builderius\Twig\NodeVisitor\NodeVisitorInterface
{
    public final function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        return $this->doEnterNode($node, $env);
    }
    public final function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        return $this->doLeaveNode($node, $env);
    }
    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    protected abstract function doEnterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env);
    /**
     * Called after child nodes are visited.
     *
     * @return Node|null The modified node or null if the node must be removed
     */
    protected abstract function doLeaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env);
}
