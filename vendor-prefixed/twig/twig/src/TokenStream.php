<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig;

use Builderius\Twig\Error\SyntaxError;
/**
 * Represents a token stream.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TokenStream
{
    private $tokens;
    private $current = 0;
    private $source;
    public function __construct(array $tokens, \Builderius\Twig\Source $source = null)
    {
        $this->tokens = $tokens;
        $this->source = $source ?: new \Builderius\Twig\Source('', '');
    }
    public function __toString()
    {
        return \implode("\n", $this->tokens);
    }
    public function injectTokens(array $tokens)
    {
        $this->tokens = \array_merge(\array_slice($this->tokens, 0, $this->current), $tokens, \array_slice($this->tokens, $this->current));
    }
    /**
     * Sets the pointer to the next token and returns the old one.
     */
    public function next() : \Builderius\Twig\Token
    {
        if (!isset($this->tokens[++$this->current])) {
            throw new \Builderius\Twig\Error\SyntaxError('Unexpected end of template.', $this->tokens[$this->current - 1]->getLine(), $this->source);
        }
        return $this->tokens[$this->current - 1];
    }
    /**
     * Tests a token, sets the pointer to the next one and returns it or throws a syntax error.
     *
     * @return Token|null The next token if the condition is true, null otherwise
     */
    public function nextIf($primary, $secondary = null)
    {
        if ($this->tokens[$this->current]->test($primary, $secondary)) {
            return $this->next();
        }
    }
    /**
     * Tests a token and returns it or throws a syntax error.
     */
    public function expect($type, $value = null, string $message = null) : \Builderius\Twig\Token
    {
        $token = $this->tokens[$this->current];
        if (!$token->test($type, $value)) {
            $line = $token->getLine();
            throw new \Builderius\Twig\Error\SyntaxError(\sprintf('%sUnexpected token "%s"%s ("%s" expected%s).', $message ? $message . '. ' : '', \Builderius\Twig\Token::typeToEnglish($token->getType()), $token->getValue() ? \sprintf(' of value "%s"', $token->getValue()) : '', \Builderius\Twig\Token::typeToEnglish($type), $value ? \sprintf(' with value "%s"', $value) : ''), $line, $this->source);
        }
        $this->next();
        return $token;
    }
    /**
     * Looks at the next token.
     */
    public function look(int $number = 1) : \Builderius\Twig\Token
    {
        if (!isset($this->tokens[$this->current + $number])) {
            throw new \Builderius\Twig\Error\SyntaxError('Unexpected end of template.', $this->tokens[$this->current + $number - 1]->getLine(), $this->source);
        }
        return $this->tokens[$this->current + $number];
    }
    /**
     * Tests the current token.
     */
    public function test($primary, $secondary = null) : bool
    {
        return $this->tokens[$this->current]->test($primary, $secondary);
    }
    /**
     * Checks if end of stream was reached.
     */
    public function isEOF() : bool
    {
        return -1 === $this->tokens[$this->current]->getType();
    }
    public function getCurrent() : \Builderius\Twig\Token
    {
        return $this->tokens[$this->current];
    }
    /**
     * Gets the source associated with this stream.
     *
     * @internal
     */
    public function getSourceContext() : \Builderius\Twig\Source
    {
        return $this->source;
    }
}