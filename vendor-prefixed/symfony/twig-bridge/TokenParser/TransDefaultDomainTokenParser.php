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

use Builderius\Symfony\Bridge\Twig\Node\TransDefaultDomainNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
use Builderius\Twig\TokenParser\AbstractTokenParser;
/**
 * Token Parser for the 'trans_default_domain' tag.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TransDefaultDomainTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        return new \Builderius\Symfony\Bridge\Twig\Node\TransDefaultDomainNode($expr, $token->getLine(), $this->getTag());
    }
    /**
     * {@inheritdoc}
     */
    public function getTag() : string
    {
        return 'trans_default_domain';
    }
}
