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
namespace Builderius\Twig\Node\Expression;

use Builderius\Twig\Compiler;
class AssignNameExpression extends \Builderius\Twig\Node\Expression\NameExpression
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('$context[')->string($this->getAttribute('name'))->raw(']');
    }
}
