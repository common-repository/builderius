<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node\Expression\Test;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\TestExpression;
/**
 * Checks that a variable is null.
 *
 *  {{ var is none }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NullTest extends \Builderius\Twig\Node\Expression\TestExpression
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('(null === ')->subcompile($this->getNode('node'))->raw(')');
    }
}
