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

use Builderius\Twig\Error\SyntaxError;
use Builderius\Twig\Node\BodyNode;
use Builderius\Twig\Node\MacroNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Token;
/**
 * Defines a macro.
 *
 *   {% macro input(name, value, type, size) %}
 *      <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
 *   {% endmacro %}
 *
 * @internal
 */
final class MacroTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(
            /* Token::NAME_TYPE */
            5
        )->getValue();
        $arguments = $this->parser->getExpressionParser()->parseArguments(\true, \true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
        if ($token = $stream->nextIf(
            /* Token::NAME_TYPE */
            5
        )) {
            $value = $token->getValue();
            if ($value != $name) {
                throw new \Builderius\Twig\Error\SyntaxError(\sprintf('Expected endmacro for macro "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }
        $this->parser->popLocalScope();
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $this->parser->setMacro($name, new \Builderius\Twig\Node\MacroNode($name, new \Builderius\Twig\Node\BodyNode([$body]), $arguments, $lineno, $this->getTag()));
        return new \Builderius\Twig\Node\Node();
    }
    public function decideBlockEnd(\Builderius\Twig\Token $token) : bool
    {
        return $token->test('endmacro');
    }
    public function getTag() : string
    {
        return 'macro';
    }
}
