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

use Builderius\Twig\Node\Expression\AssignNameExpression;
use Builderius\Twig\Node\ImportNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Imports macros.
 *
 *   {% import 'forms.html' as forms %}
 *
 * @internal
 */
final class ImportTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(
            /* Token::NAME_TYPE */
            5,
            'as'
        );
        $var = new \Builderius\Twig\Node\Expression\AssignNameExpression($this->parser->getStream()->expect(
            /* Token::NAME_TYPE */
            5
        )->getValue(), $token->getLine());
        $this->parser->getStream()->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $this->parser->addImportedSymbol('template', $var->getAttribute('name'));
        return new \Builderius\Twig\Node\ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
    }
    public function getTag() : string
    {
        return 'import';
    }
}
