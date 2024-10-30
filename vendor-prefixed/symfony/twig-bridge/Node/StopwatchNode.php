<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Node;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\AssignNameExpression;
use Builderius\Twig\Node\Node;
/**
 * Represents a stopwatch node.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
final class StopwatchNode extends \Builderius\Twig\Node\Node
{
    public function __construct(\Builderius\Twig\Node\Node $name, \Builderius\Twig\Node\Node $body, \Builderius\Twig\Node\Expression\AssignNameExpression $var, int $lineno = 0, string $tag = null)
    {
        parent::__construct(['body' => $body, 'name' => $name, 'var' => $var], [], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write('')->subcompile($this->getNode('var'))->raw(' = ')->subcompile($this->getNode('name'))->write(";\n")->write("\$this->env->getExtension('Symfony\\Bridge\\Twig\\Extension\\StopwatchExtension')->getStopwatch()->start(")->subcompile($this->getNode('var'))->raw(", 'template');\n")->subcompile($this->getNode('body'))->write("\$this->env->getExtension('Symfony\\Bridge\\Twig\\Extension\\StopwatchExtension')->getStopwatch()->stop(")->subcompile($this->getNode('var'))->raw(");\n");
    }
}
