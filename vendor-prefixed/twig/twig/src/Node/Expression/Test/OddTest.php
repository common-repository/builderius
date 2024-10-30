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
 * Checks if a number is odd.
 *
 *  {{ var is odd }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class OddTest extends \Builderius\Twig\Node\Expression\TestExpression
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('(')->subcompile($this->getNode('node'))->raw(' % 2 != 0')->raw(')');
    }
}
