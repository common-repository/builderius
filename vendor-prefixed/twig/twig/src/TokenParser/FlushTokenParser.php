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

use Builderius\Twig\Node\FlushNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Flushes the output to the client.
 *
 * @see flush()
 *
 * @internal
 */
final class FlushTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $this->parser->getStream()->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \Builderius\Twig\Node\FlushNode($token->getLine(), $this->getTag());
    }
    public function getTag() : string
    {
        return 'flush';
    }
}
