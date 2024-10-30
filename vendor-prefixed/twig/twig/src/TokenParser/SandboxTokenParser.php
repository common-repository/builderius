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
use Builderius\Twig\Node\IncludeNode;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\SandboxNode;
use Builderius\Twig\Node\TextNode;
use Builderius\Twig\Token;
/**
 * Marks a section of a template as untrusted code that must be evaluated in the sandbox mode.
 *
 *    {% sandbox %}
 *        {% include 'user.html' %}
 *    {% endsandbox %}
 *
 * @see https://twig.symfony.com/doc/api.html#sandbox-extension for details
 *
 * @internal
 */
final class SandboxTokenParser extends \Builderius\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\Builderius\Twig\Token $token) : \Builderius\Twig\Node\Node
    {
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        // in a sandbox tag, only include tags are allowed
        if (!$body instanceof \Builderius\Twig\Node\IncludeNode) {
            foreach ($body as $node) {
                if ($node instanceof \Builderius\Twig\Node\TextNode && \ctype_space($node->getAttribute('data'))) {
                    continue;
                }
                if (!$node instanceof \Builderius\Twig\Node\IncludeNode) {
                    throw new \Builderius\Twig\Error\SyntaxError('Only "include" tags are allowed within a "sandbox" section.', $node->getTemplateLine(), $stream->getSourceContext());
                }
            }
        }
        return new \Builderius\Twig\Node\SandboxNode($body, $token->getLine(), $this->getTag());
    }
    public function decideBlockEnd(\Builderius\Twig\Token $token) : bool
    {
        return $token->test('endsandbox');
    }
    public function getTag() : string
    {
        return 'sandbox';
    }
}
