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
use Builderius\Twig\Node\Node;
class FilterExpression extends \Builderius\Twig\Node\Expression\CallExpression
{
    public function __construct(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Node\Expression\ConstantExpression $filterName, \Builderius\Twig\Node\Node $arguments, int $lineno, string $tag = null)
    {
        parent::__construct(['node' => $node, 'filter' => $filterName, 'arguments' => $arguments], [], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $name = $this->getNode('filter')->getAttribute('value');
        $filter = $compiler->getEnvironment()->getFilter($name);
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'filter');
        $this->setAttribute('needs_environment', $filter->needsEnvironment());
        $this->setAttribute('needs_context', $filter->needsContext());
        $this->setAttribute('arguments', $filter->getArguments());
        $this->setAttribute('callable', $filter->getCallable());
        $this->setAttribute('is_variadic', $filter->isVariadic());
        $this->compileCallable($compiler);
    }
}
