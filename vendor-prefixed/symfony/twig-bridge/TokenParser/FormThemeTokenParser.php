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

use Builderius\Symfony\Bridge\Twig\Node\FormThemeNode;
use Builderius\Twig\Node\Expression\ArrayExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
use Builderius\Twig\TokenParser\AbstractTokenParser;
/**
 * Token Parser for the 'form_theme' tag.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class FormThemeTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $form = $this->parser->getExpressionParser()->parseExpression();
        $only = \false;
        if ($this->parser->getStream()->test(\Builderius\Twig\Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();
            $resources = $this->parser->getExpressionParser()->parseExpression();
            if ($this->parser->getStream()->nextIf(\Builderius\Twig\Token::NAME_TYPE, 'only')) {
                $only = \true;
            }
        } else {
            $resources = new \Builderius\Twig\Node\Expression\ArrayExpression([], $stream->getCurrent()->getLine());
            do {
                $resources->addElement($this->parser->getExpressionParser()->parseExpression());
            } while (!$stream->test(\Builderius\Twig\Token::BLOCK_END_TYPE));
        }
        $stream->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        return new \Builderius\Symfony\Bridge\Twig\Node\FormThemeNode($form, $resources, $lineno, $this->getTag(), $only);
    }
    /**
     * {@inheritdoc}
     */
    public function getTag() : string
    {
        return 'form_theme';
    }
}
