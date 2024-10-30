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
use Builderius\Twig\Error\SyntaxError;
use Builderius\Twig\Node\Expression\ArrayExpression;
use Builderius\Twig\Node\Expression\BlockReferenceExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FunctionExpression;
use Builderius\Twig\Node\Expression\GetAttrExpression;
use Builderius\Twig\Node\Expression\MethodCallExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\Expression\TestExpression;
use Builderius\Twig\Node\Node;
/**
 * Checks if a variable is defined in the current context.
 *
 *    {# defined works with variable names and variable attributes #}
 *    {% if foo is defined %}
 *        {# ... #}
 *    {% endif %}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DefinedTest extends \Builderius\Twig\Node\Expression\TestExpression
{
    public function __construct(\Builderius\Twig\Node\Node $node, string $name, ?\Builderius\Twig\Node\Node $arguments, int $lineno)
    {
        if ($node instanceof \Builderius\Twig\Node\Expression\NameExpression) {
            $node->setAttribute('is_defined_test', \true);
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\GetAttrExpression) {
            $node->setAttribute('is_defined_test', \true);
            $this->changeIgnoreStrictCheck($node);
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\BlockReferenceExpression) {
            $node->setAttribute('is_defined_test', \true);
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\FunctionExpression && 'constant' === $node->getAttribute('name')) {
            $node->setAttribute('is_defined_test', \true);
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\ConstantExpression || $node instanceof \Builderius\Twig\Node\Expression\ArrayExpression) {
            $node = new \Builderius\Twig\Node\Expression\ConstantExpression(\true, $node->getTemplateLine());
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\MethodCallExpression) {
            $node->setAttribute('is_defined_test', \true);
        } else {
            throw new \Builderius\Twig\Error\SyntaxError('The "defined" test only works with simple variables.', $lineno);
        }
        parent::__construct($node, $name, $arguments, $lineno);
    }
    private function changeIgnoreStrictCheck(\Builderius\Twig\Node\Expression\GetAttrExpression $node)
    {
        $node->setAttribute('optimizable', \false);
        $node->setAttribute('ignore_strict_check', \true);
        if ($node->getNode('node') instanceof \Builderius\Twig\Node\Expression\GetAttrExpression) {
            $this->changeIgnoreStrictCheck($node->getNode('node'));
        }
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
