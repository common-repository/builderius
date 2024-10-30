<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\NodeVisitor;

use Builderius\Symfony\Bridge\Twig\Node\TransDefaultDomainNode;
use Builderius\Symfony\Bridge\Twig\Node\TransNode;
use Builderius\Twig\Environment;
use Builderius\Twig\Node\BlockNode;
use Builderius\Twig\Node\Expression\ArrayExpression;
use Builderius\Twig\Node\Expression\AssignNameExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FilterExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\ModuleNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\SetNode;
use Builderius\Twig\NodeVisitor\AbstractNodeVisitor;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TranslationDefaultDomainNodeVisitor extends \Builderius\Twig\NodeVisitor\AbstractNodeVisitor
{
    private $scope;
    public function __construct()
    {
        $this->scope = new \Builderius\Symfony\Bridge\Twig\NodeVisitor\Scope();
    }
    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Twig\Node\BlockNode || $node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->scope = $this->scope->enter();
        }
        if ($node instanceof \Builderius\Symfony\Bridge\Twig\Node\TransDefaultDomainNode) {
            if ($node->getNode('expr') instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
                $this->scope->set('domain', $node->getNode('expr'));
                return $node;
            } else {
                $var = $this->getVarName();
                $name = new \Builderius\Twig\Node\Expression\AssignNameExpression($var, $node->getTemplateLine());
                $this->scope->set('domain', new \Builderius\Twig\Node\Expression\NameExpression($var, $node->getTemplateLine()));
                return new \Builderius\Twig\Node\SetNode(\false, new \Builderius\Twig\Node\Node([$name]), new \Builderius\Twig\Node\Node([$node->getNode('expr')]), $node->getTemplateLine());
            }
        }
        if (!$this->scope->has('domain')) {
            return $node;
        }
        if ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression && \in_array($node->getNode('filter')->getAttribute('value'), ['trans', 'transchoice'])) {
            $arguments = $node->getNode('arguments');
            $ind = 'trans' === $node->getNode('filter')->getAttribute('value') ? 1 : 2;
            if ($this->isNamedArguments($arguments)) {
                if (!$arguments->hasNode('domain') && !$arguments->hasNode($ind)) {
                    $arguments->setNode('domain', $this->scope->get('domain'));
                }
            } else {
                if (!$arguments->hasNode($ind)) {
                    if (!$arguments->hasNode($ind - 1)) {
                        $arguments->setNode($ind - 1, new \Builderius\Twig\Node\Expression\ArrayExpression([], $node->getTemplateLine()));
                    }
                    $arguments->setNode($ind, $this->scope->get('domain'));
                }
            }
        } elseif ($node instanceof \Builderius\Symfony\Bridge\Twig\Node\TransNode) {
            if (!$node->hasNode('domain')) {
                $node->setNode('domain', $this->scope->get('domain'));
            }
        }
        return $node;
    }
    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        if ($node instanceof \Builderius\Symfony\Bridge\Twig\Node\TransDefaultDomainNode) {
            return null;
        }
        if ($node instanceof \Builderius\Twig\Node\BlockNode || $node instanceof \Builderius\Twig\Node\ModuleNode) {
            $this->scope = $this->scope->leave();
        }
        return $node;
    }
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        return -10;
    }
    private function isNamedArguments(\Builderius\Twig\Node\Node $arguments) : bool
    {
        foreach ($arguments as $name => $node) {
            if (!\is_int($name)) {
                return \true;
            }
        }
        return \false;
    }
    private function getVarName() : string
    {
        return \sprintf('__internal_%s', \hash('sha256', \uniqid(\mt_rand(), \true), \false));
    }
}
