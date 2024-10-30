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

use Builderius\Symfony\Bridge\Twig\Node\DumpNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
use Builderius\Twig\TokenParser\AbstractTokenParser;
/**
 * Token Parser for the 'dump' tag.
 *
 * Dump variables with:
 *
 *     {% dump %}
 *     {% dump foo %}
 *     {% dump foo, bar %}
 *
 * @author Julien Galenski <julien.galenski@gmail.com>
 */
final class DumpTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $values = null;
        if (!$this->parser->getStream()->test(\Builderius\Twig\Token::BLOCK_END_TYPE)) {
            $values = $this->parser->getExpressionParser()->parseMultitargetExpression();
        }
        $this->parser->getStream()->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        return new \Builderius\Symfony\Bridge\Twig\Node\DumpNode($this->parser->getVarName(), $values, $token->getLine(), $this->getTag());
    }
    /**
     * {@inheritdoc}
     */
    public function getTag() : string
    {
        return 'dump';
    }
}
