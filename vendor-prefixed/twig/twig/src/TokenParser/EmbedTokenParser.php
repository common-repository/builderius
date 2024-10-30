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

use Builderius\Twig\Node\EmbedNode;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Embeds a template.
 *
 * @internal
 */
final class EmbedTokenParser extends \Builderius\Twig\TokenParser\IncludeTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $stream = $this->parser->getStream();
        $parent = $this->parser->getExpressionParser()->parseExpression();
        list($variables, $only, $ignoreMissing) = $this->parseArguments();
        $parentToken = $fakeParentToken = new \Builderius\Twig\Token(
            /* Token::STRING_TYPE */
            7,
            '__parent__',
            $token->getLine()
        );
        if ($parent instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            $parentToken = new \Builderius\Twig\Token(
                /* Token::STRING_TYPE */
                7,
                $parent->getAttribute('value'),
                $token->getLine()
            );
        } elseif ($parent instanceof \Builderius\Twig\Node\Expression\NameExpression) {
            $parentToken = new \Builderius\Twig\Token(
                /* Token::NAME_TYPE */
                5,
                $parent->getAttribute('name'),
                $token->getLine()
            );
        }
        // inject a fake parent to make the parent() function work
        $stream->injectTokens([new \Builderius\Twig\Token(
            /* Token::BLOCK_START_TYPE */
            1,
            '',
            $token->getLine()
        ), new \Builderius\Twig\Token(
            /* Token::NAME_TYPE */
            5,
            'extends',
            $token->getLine()
        ), $parentToken, new \Builderius\Twig\Token(
            /* Token::BLOCK_END_TYPE */
            3,
            '',
            $token->getLine()
        )]);
        $module = $this->parser->parse($stream, [$this, 'decideBlockEnd'], \true);
        // override the parent with the correct one
        if ($fakeParentToken === $parentToken) {
            $module->setNode('parent', $parent);
        }
        $this->parser->embedTemplate($module);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \Builderius\Twig\Node\EmbedNode($module->getTemplateName(), $module->getAttribute('index'), $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }
    public function decideBlockEnd(\Builderius\Twig\Token $token) : bool
    {
        return $token->test('endembed');
    }
    public function getTag() : string
    {
        return 'embed';
    }
}
