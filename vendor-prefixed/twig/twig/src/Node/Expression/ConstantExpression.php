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
class ConstantExpression extends \Builderius\Twig\Node\Expression\AbstractExpression
{
    public function __construct($value, int $lineno)
    {
        parent::__construct([], ['value' => $value], $lineno);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->repr($this->getAttribute('value'));
    }
}
