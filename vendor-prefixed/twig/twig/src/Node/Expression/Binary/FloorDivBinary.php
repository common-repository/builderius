<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node\Expression\Binary;

use Builderius\Twig\Compiler;
class FloorDivBinary extends \Builderius\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('(int) floor(');
        parent::compile($compiler);
        $compiler->raw(')');
    }
    public function operator(\Builderius\Twig\Compiler $compiler) : \Builderius\Twig\Compiler
    {
        return $compiler->raw('/');
    }
}
