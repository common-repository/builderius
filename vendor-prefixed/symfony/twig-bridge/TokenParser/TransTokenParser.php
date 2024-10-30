<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\TokenParser;

use Builderius\Symfony\Bridge\Twig\Node\TransNode;
use Builderius\Twig\Error\SyntaxError;
use Builderius\Twig\Node\Expression\AbstractExpression;
use Builderius\Twig\Node\Expression\ArrayExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\TextNode;
use Builderius\Twig\Token;
use Builderius\Twig\TokenParser\AbstractTokenParser;
/**
 * Token Parser for the 'trans' tag.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TransTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $vars = new \Builderius\Twig\Node\Expression\ArrayExpression([], $lineno);
        $domain = null;
        $locale = null;
        if (!$stream->test(\Builderius\Twig\Token::BLOCK_END_TYPE)) {
            if ($stream->test('count')) {
                // {% trans count 5 %}
                $stream->next();
                $count = $this->parser->getExpressionParser()->parseExpression();
            }
            if ($stream->test('with')) {
                // {% trans with vars %}
                $stream->next();
                $vars = $this->parser->getExpressionParser()->parseExpression();
            }
            if ($stream->test('from')) {
                // {% trans from "messages" %}
                $stream->next();
                $domain = $this->parser->getExpressionParser()->parseExpression();
            }
            if ($stream->test('into')) {
                // {% trans into "fr" %}
                $stream->next();
                $locale = $this->parser->getExpressionParser()->parseExpression();
            } elseif (!$stream->test(\Builderius\Twig\Token::BLOCK_END_TYPE)) {
                throw new \Builderius\Twig\Error\SyntaxError('Unexpected token. Twig was looking for the "with", "from", or "into" keyword.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }
        // {% trans %}message{% endtrans %}
        $stream->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideTransFork'], \true);
        if (!$body instanceof \Builderius\Twig\Node\TextNode && !$body instanceof \Builderius\Twig\Node\Expression\AbstractExpression) {
            throw new \Builderius\Twig\Error\SyntaxError('A message inside a trans tag must be a simple text.', $body->getTemplateLine(), $stream->getSourceContext());
        }
        $stream->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        return new \Builderius\Symfony\Bridge\Twig\Node\TransNode($body, $domain, $count, $vars, $locale, $lineno, $this->getTag());
    }
    public function decideTransFork(\Builderius\Twig\Token $token) : bool
    {
        return $token->test(['endtrans']);
    }
    /**
     * {@inheritdoc}
     */
    public function getTag() : string
    {
        return 'trans';
    }
}
