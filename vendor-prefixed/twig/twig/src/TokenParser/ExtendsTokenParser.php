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
namespace Builderius\Twig\TokenParser;

use Builderius\Twig\Error\SyntaxError;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Extends a template by another one.
 *
 *  {% extends "base.html" %}
 *
 * @internal
 */
final class ExtendsTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $stream = $this->parser->getStream();
        if ($this->parser->peekBlockStack()) {
            throw new \Builderius\Twig\Error\SyntaxError('Cannot use "extend" in a block.', $token->getLine(), $stream->getSourceContext());
        } elseif (!$this->parser->isMainScope()) {
            throw new \Builderius\Twig\Error\SyntaxError('Cannot use "extend" in a macro.', $token->getLine(), $stream->getSourceContext());
        }
        if (null !== $this->parser->getParent()) {
            throw new \Builderius\Twig\Error\SyntaxError('Multiple extends tags are forbidden.', $token->getLine(), $stream->getSourceContext());
        }
        $this->parser->setParent($this->parser->getExpressionParser()->parseExpression());
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \Builderius\Twig\Node\Node();
    }
    public function getTag() : string
    {
        return 'extends';
    }
}
