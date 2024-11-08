<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node;

use Builderius\Twig\Compiler;
/**
 * Represents a flush node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FlushNode extends \Builderius\Twig\Node\Node
{
    public function __construct(int $lineno, string $tag)
    {
        parent::__construct([], [], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write("flush();\n");
    }
}
