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
class TestExpression extends \Builderius\Twig\Node\Expression\CallExpression
{
    public function __construct(\Builderius\Twig\Node\Node $node, string $name, ?\Builderius\Twig\Node\Node $arguments, int $lineno)
    {
        $nodes = ['node' => $node];
        if (null !== $arguments) {
            $nodes['arguments'] = $arguments;
        }
        parent::__construct($nodes, ['name' => $name], $lineno);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $name = $this->getAttribute('name');
        $test = $compiler->getEnvironment()->getTest($name);
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'test');
        $this->setAttribute('arguments', $test->getArguments());
        $this->setAttribute('callable', $test->getCallable());
        $this->setAttribute('is_variadic', $test->isVariadic());
        $this->compileCallable($compiler);
    }
}
