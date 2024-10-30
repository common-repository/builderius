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
namespace Builderius\Twig\Node;

use Builderius\Twig\Compiler;
/**
 * Represents a text node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TextNode extends \Builderius\Twig\Node\Node implements \Builderius\Twig\Node\NodeOutputInterface
{
    public function __construct(string $data, int $lineno)
    {
        parent::__construct([], ['data' => $data], $lineno);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write('echo ')->string($this->getAttribute('data'))->raw(";\n");
    }
}
