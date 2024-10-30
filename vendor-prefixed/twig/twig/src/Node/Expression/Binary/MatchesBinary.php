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
class MatchesBinary extends \Builderius\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('preg_match(')->subcompile($this->getNode('right'))->raw(', ')->subcompile($this->getNode('left'))->raw(')');
    }
    public function operator(\Builderius\Twig\Compiler $compiler) : \Builderius\Twig\Compiler
    {
        return $compiler->raw('');
    }
}
