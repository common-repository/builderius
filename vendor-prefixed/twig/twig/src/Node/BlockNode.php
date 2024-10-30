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
 * Represents a block node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class BlockNode extends \Builderius\Twig\Node\Node
{
    public function __construct(string $name, \Builderius\Twig\Node\Node $body, int $lineno, string $tag = null)
    {
        parent::__construct(['body' => $body], ['name' => $name], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write(\sprintf("public function block_%s(\$context, array \$blocks = [])\n", $this->getAttribute('name')), "{\n")->indent()->write("\$macros = \$this->macros;\n");
        $compiler->subcompile($this->getNode('body'))->outdent()->write("}\n\n");
    }
}
