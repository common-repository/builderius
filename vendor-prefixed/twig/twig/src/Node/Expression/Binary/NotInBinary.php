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
class NotInBinary extends \Builderius\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('!twig_in_filter(')->subcompile($this->getNode('left'))->raw(', ')->subcompile($this->getNode('right'))->raw(')');
    }
    public function operator(\Builderius\Twig\Compiler $compiler) : \Builderius\Twig\Compiler
    {
        return $compiler->raw('not in');
    }
}
