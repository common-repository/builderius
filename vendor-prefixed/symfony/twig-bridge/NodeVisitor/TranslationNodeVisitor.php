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

use Builderius\Symfony\Bridge\Twig\Node\TransNode;
use Builderius\Twig\Environment;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FilterExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\NodeVisitor\AbstractNodeVisitor;
/**
 * TranslationNodeVisitor extracts translation messages.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TranslationNodeVisitor extends \Builderius\Twig\NodeVisitor\AbstractNodeVisitor
{
    const UNDEFINED_DOMAIN = '_undefined';
    private $enabled = \false;
    private $messages = [];
    public function enable() : void
    {
        $this->enabled = \true;
        $this->messages = [];
    }
    public function disable() : void
    {
        $this->enabled = \false;
        $this->messages = [];
    }
    public function getMessages() : array
    {
        return $this->messages;
    }
    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : \Builderius\Twig\Node\Node
    {
        if (!$this->enabled) {
            return $node;
        }
        if ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression && 'trans' === $node->getNode('filter')->getAttribute('value') && $node->getNode('node') instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            // extract constant nodes with a trans filter
            $this->messages[] = [$node->getNode('node')->getAttribute('value'), $this->getReadDomainFromArguments($node->getNode('arguments'), 1)];
        } elseif ($node instanceof \Builderius\Twig\Node\Expression\FilterExpression && 'transchoice' === $node->getNode('filter')->getAttribute('value') && $node->getNode('node') instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            // extract constant nodes with a trans filter
            $this->messages[] = [$node->getNode('node')->getAttribute('value'), $this->getReadDomainFromArguments($node->getNode('arguments'), 2)];
        } elseif ($node instanceof \Builderius\Symfony\Bridge\Twig\Node\TransNode) {
            // extract trans nodes
            $this->messages[] = [$node->getNode('body')->getAttribute('data'), $node->hasNode('domain') ? $this->getReadDomainFromNode($node->getNode('domain')) : null];
        }
        return $node;
    }
    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(\Builderius\Twig\Node\Node $node, \Builderius\Twig\Environment $env) : ?\Builderius\Twig\Node\Node
    {
        return $node;
    }
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        return 0;
    }
    private function getReadDomainFromArguments(\Builderius\Twig\Node\Node $arguments, int $index) : ?string
    {
        if ($arguments->hasNode('domain')) {
            $argument = $arguments->getNode('domain');
        } elseif ($arguments->hasNode($index)) {
            $argument = $arguments->getNode($index);
        } else {
            return null;
        }
        return $this->getReadDomainFromNode($argument);
    }
    private function getReadDomainFromNode(\Builderius\Twig\Node\Node $node) : ?string
    {
        if ($node instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            return $node->getAttribute('value');
        }
        return self::UNDEFINED_DOMAIN;
    }
}
