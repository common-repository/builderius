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
class NegUnary extends \Builderius\Twig\Node\Expression\Unary\AbstractUnary
{
    public function operator(\Builderius\Twig\Compiler $compiler) : \Builderius\Twig\Compiler
    {
        return $compiler->raw('-');
    }
}
