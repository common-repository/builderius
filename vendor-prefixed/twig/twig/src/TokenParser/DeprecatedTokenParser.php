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

use Builderius\Twig\Node\DeprecatedNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Deprecates a section of a template.
 *
 *    {% deprecated 'The "base.twig" template is deprecated, use "layout.twig" instead.' %}
 *    {% extends 'layout.html.twig' %}
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @internal
 */
final class DeprecatedTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        return new \Builderius\Twig\Node\DeprecatedNode($expr, $token->getLine(), $this->getTag());
    }
    public function getTag() : string
    {
        return 'deprecated';
    }
}
