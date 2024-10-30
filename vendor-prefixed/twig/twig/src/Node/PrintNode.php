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
namespace Builderius\Twig\Node;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\AbstractExpression;
/**
 * Represents a node that outputs an expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PrintNode extends \Builderius\Twig\Node\Node implements \Builderius\Twig\Node\NodeOutputInterface
{
    public function __construct(\Builderius\Twig\Node\Expression\AbstractExpression $expr, int $lineno, string $tag = null)
    {
        parent::__construct(['expr' => $expr], [], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write('echo ')->subcompile($this->getNode('expr'))->raw(";\n");
    }
}
