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

use Builderius\Twig\Node\IncludeNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Includes a template.
 *
 *   {% include 'header.html' %}
 *     Body
 *   {% include 'footer.html' %}
 *
 * @internal
 */
class IncludeTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        list($variables, $only, $ignoreMissing) = $this->parseArguments();
        return new \Builderius\Twig\Node\IncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }
    protected function parseArguments()
    {
        $stream = $this->parser->getStream();
        $ignoreMissing = \false;
        if ($stream->nextIf(
            /* Token::NAME_TYPE */
            5,
            'ignore'
        )) {
            $stream->expect(
                /* Token::NAME_TYPE */
                5,
                'missing'
            );
            $ignoreMissing = \true;
        }
        $variables = null;
        if ($stream->nextIf(
            /* Token::NAME_TYPE */
            5,
            'with'
        )) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }
        $only = \false;
        if ($stream->nextIf(
            /* Token::NAME_TYPE */
            5,
            'only'
        )) {
            $only = \true;
        }
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return [$variables, $only, $ignoreMissing];
    }
    public function getTag() : string
    {
        return 'include';
    }
}