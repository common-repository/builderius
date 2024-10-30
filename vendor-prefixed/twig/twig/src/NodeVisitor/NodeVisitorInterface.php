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
 * Interface for node visitor classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface NodeVisitorInterface
{
    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    public function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node;
    /**
     * Called after child nodes are visited.
     *
     * @return Node|null The modified node or null if the node must be removed
     */
    public function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node;
    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return int The priority level
     */
    public function getPriority();
}
