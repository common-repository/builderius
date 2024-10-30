<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\NodeVisitor;

use Builderius\Twig\Environment;
use Builderius\Twig\Node\Expression\AssignNameExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\GetAttrExpression;
use Builderius\Twig\Node\Expression\MethodCallExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\ImportNode;
use Builderius\Twig\Node\ModuleNode;
use Builderius\Twig\Node\Node;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class MacroAutoImportNodeVisitor implements \Builderius\Twig\NodeVisitor\NodeVisitorInterface
{
    private $inAModule = \false;
    private $hasMacroCalls = \false;
    public function enterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->inAModule = \true;
            $this->hasMacroCalls = \false;
        }
        return $node;
    }
    public function leaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->inAModule = \false;
            if ($this->hasMacroCalls) {
                $node->getNode('constructor_end')->setNode('_auto_macro_import', new \Builderius\Twig\Node\ImportNode(new \Builderius\Twig\Node\Expression\NameExpression('_self', 0), new \Builderius\Twig\Node\Expression\AssignNameExpression('_self', 0), 0, 'import', \true));
            }
        } elseif ($this->inAModule) {
            if ($node instanceof \Builderius\Twig\Node\Expression\GetAttrExpression && $node->getNode('node') instanceof \Builderius\Twig\Node\Expression\NameExpression && '_self' === $node->getNode('node')->getAttribute('name') && $node->getNode('attribute') instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
                $this->hasMacroCalls = \true;
                $name = $node->getNode('attribute')->getAttribute('value');
                $node = new \Builderius\Twig\Node\Expression\MethodCallExpression($node->getNode('node'), 'macro_' . $name, $node->getNode('arguments'), $node->getTemplateLine());
                $node->setAttribute('safe', \true);
            }
        }
        return $node;
    }
    public function getPriority() : int
    {
        // we must be ran before auto-escaping
        return -10;
    }
}
