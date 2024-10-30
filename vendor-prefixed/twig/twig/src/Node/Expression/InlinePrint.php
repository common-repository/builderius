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
use Builderius\Twig\Node\Node;
/**
 * @internal
 */
final class InlinePrint extends \Builderius\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\Builderius\Twig\Node\Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->raw('print (')->subcompile($this->getNode('node'))->raw(')');
    }
}
