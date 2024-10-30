<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node\Expression\Filter;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\ConditionalExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FilterExpression;
use Builderius\Twig\Node\Expression\GetAttrExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\Expression\Test\DefinedTest;
use Builderius\Twig\Node\Node;
/**
 * Returns the value or the default value when it is undefined or empty.
 *
 *  {{ var.foo|default('foo item on var is not defined') }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DefaultFilter extends \Builderius\Twig\Node\Expression\FilterExpression
{
    public function __construct(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Node\Expression\ConstantExpression $filterName, \Builderius\Twig\Node\Node $arguments, int $lineno, string $tag = null)
    {
        $default = new \Builderius\Twig\Node\Expression\FilterExpression($node, new \Builderius\Twig\Node\Expression\ConstantExpression('default', $node->getTemplateLine()), $arguments, $node->getTemplateLine());
        if ('default' === $filterName->getAttribute('value') && ($node instanceof \Builderius\Twig\Node\Expression\NameExpression || $node instanceof \Builderius\Twig\Node\Expression\GetAttrExpression)) {
            $test = new \Builderius\Twig\Node\Expression\Test\DefinedTest(clone $node, 'defined', new \Builderius\Twig\Node\Node(), $node->getTemplateLine());
            $false = \count($arguments) ? $arguments->getNode(0) : new \Builderius\Twig\Node\Expression\ConstantExpression('', $node->getTemplateLine());
            $node = new \Builderius\Twig\Node\Expression\ConditionalExpression($test, $default, $false, $node->getTemplateLine());
        } else {
            $node = $default;
        }
        parent::__construct($node, $filterName, $arguments, $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
