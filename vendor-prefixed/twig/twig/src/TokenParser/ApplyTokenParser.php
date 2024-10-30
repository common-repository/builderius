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

use Builderius\Twig\Node\Expression\TempNameExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\PrintNode;
use Builderius\Twig\Node\SetNode;
use Builderius\Twig\Token;
/**
 * Applies filters on a section of a template.
 *
 *   {% apply upper %}
 *      This text becomes uppercase
 *   {% endapply %}
 *
 * @internal
 */
final class ApplyTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $name = $this->parser->getVarName();
        $ref = new \Builderius\Twig\Node\Expression\TempNameExpression($name, $lineno);
        $ref->setAttribute('always_defined', \true);
        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideApplyEnd'], \true);
        $this->parser->getStream()->expect(\Builderius\Twig\Token::BLOCK_END_TYPE);
        return new \Builderius\Twig\Node\Node([new \Builderius\Twig\Node\SetNode(\true, $ref, $body, $lineno, $this->getTag()), new \Builderius\Twig\Node\PrintNode($filter, $lineno, $this->getTag())]);
    }
    public function decideApplyEnd(\Builderius\Twig\Token $token) : bool
    {
        return $token->test('endapply');
    }
    public function getTag() : string
    {
        return 'apply';
    }
}
