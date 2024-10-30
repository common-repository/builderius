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

use Builderius\Twig\Node\DoNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Evaluates an expression, discarding the returned value.
 *
 * @internal
 */
final class DoTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \Builderius\Twig\Node\DoNode($expr, $token->getLine(), $this->getTag());
    }
    public function getTag() : string
    {
        return 'do';
    }
}
