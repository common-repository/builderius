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
namespace Builderius\Twig\Node\Expression\Binary;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\AbstractExpression;
use Builderius\Twig\Node\Node;
abstract class AbstractBinary extends \Builderius\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\Builderius\Twig\Node\Node $left, \Builderius\Twig\Node\Node $right, int $lineno)
    {
        parent::__construct(['left' => $left, 'right' => $right], [], $lineno);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('(')->subcompile($this->getNode('left'))->raw(' ');
        $this->operator($compiler);
        $compiler->raw(' ')->subcompile($this->getNode('right'))->raw(')');
    }
    public abstract function operator(\Builderius\Twig\Compiler $compiler) : \Builderius\Twig\Compiler;
}
