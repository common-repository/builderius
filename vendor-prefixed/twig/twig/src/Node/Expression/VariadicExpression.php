<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node\Expression;

use Builderius\Twig\Compiler;
class VariadicExpression extends \Builderius\Twig\Node\Expression\ArrayExpression
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('...');
        parent::compile($compiler);
    }
}
