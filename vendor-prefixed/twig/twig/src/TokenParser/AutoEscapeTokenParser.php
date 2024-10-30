<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\TokenParser;

use Builderius\Twig\Error\SyntaxError;
use Builderius\Twig\Node\AutoEscapeNode;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Marks a section of a template to be escaped or not.
 *
 * @internal
 */
final class AutoEscapeTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        if ($stream->test(
            /* Token::BLOCK_END_TYPE */
            3
        )) {
            $value = 'html';
        } else {
            $expr = $this->parser->getExpressionParser()->parseExpression();
            if (!$expr instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
                throw new \Builderius\Twig\Error\SyntaxError('An escaping strategy must be a string or false.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
            $value = $expr->getAttribute('value');
        }
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \Builderius\Twig\Node\AutoEscapeNode($value, $body, $lineno, $this->getTag());
    }
    public function decideBlockEnd(\Builderius\Twig\Token $token) : bool
    {
        return $token->test('endautoescape');
    }
    public function getTag() : string
    {
        return 'autoescape';
    }
}
