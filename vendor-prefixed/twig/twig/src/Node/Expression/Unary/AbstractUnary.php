<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node\Expression\Unary;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\AbstractExpression;
use Builderius\Twig\Node\Node;
abstract class AbstractUnary extends \Builderius\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\Builderius\Twig\Node\Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw(' ');
        $this->operator($compiler);
        $compiler->subcompile($this->getNode('node'));
    }
    public abstract function operator(\Builderius\Twig\Compiler $compiler) : \Builderius\Twig\Compiler;
}
