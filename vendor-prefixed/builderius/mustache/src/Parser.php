<?php

namespace Builderius\Mustache;

use Builderius\Mustache\Exception\SyntaxException;
/**
 * Mustache Parser class.
 *
 * This class is responsible for turning a set of Mustache tokens into a parse tree.
 */
class Parser
{
    private $lineNum;
    private $lineTokens;
    private $pragmas;
    private $defaultPragmas = array();
    private $pragmaFilters;
    private $pragmaBlocks;
    /**
     * Process an array of Mustache tokens and convert them into a parse tree.
     *
     * @param array $tokens Set of Mustache tokens
     *
     * @return array Mustache token parse tree
     */
    public function parse(array $tokens = array())
    {
        $this->lineNum = -1;
        $this->lineTokens = 0;
        $this->pragmas = $this->defaultPragmas;
        $this->pragmaFilters = isset($this->pragmas[\Builderius\Mustache\Engine::PRAGMA_FILTERS]);
        $this->pragmaBlocks = isset($this->pragmas[\Builderius\Mustache\Engine::PRAGMA_BLOCKS]);
        return $this->buildTree($tokens);
    }
    /**
     * Enable pragmas across all templates, regardless of the presence of pragma
     * tags in the individual templates.
     *
     * @internal Users should set global pragmas in Engine, not here :)
     *
     * @param string[] $pragmas
     */
    public function setPragmas(array $pragmas)
    {
        $this->pragmas = array();
        foreach ($pragmas as $pragma) {
            $this->enablePragma($pragma);
        }
        $this->defaultPragmas = $this->pragmas;
    }
    /**
     * Helper method for recursively building a parse tree.
     *
     * @throws SyntaxException when nesting errors or mismatched section tags are encountered
     *
     * @param array &$tokens Set of Mustache tokens
     * @param array $parent  Parent token (default: null)
     *
     * @return array Mustache Token parse tree
     */
    private function buildTree(array &$tokens, array $parent = null)
    {
        $nodes = array();
        while (!empty($tokens)) {
            $token = \array_shift($tokens);
            if ($token[\Builderius\Mustache\Tokenizer::LINE] === $this->lineNum) {
                $this->lineTokens++;
            } else {
                $this->lineNum = $token[\Builderius\Mustache\Tokenizer::LINE];
                $this->lineTokens = 0;
            }
            if ($this->pragmaFilters && isset($token[\Builderius\Mustache\Tokenizer::NAME])) {
                list($name, $filters) = $this->getNameAndFilters($token[\Builderius\Mustache\Tokenizer::NAME]);
                if (!empty($filters)) {
                    $token[\Builderius\Mustache\Tokenizer::NAME] = $name;
                    $token[\Builderius\Mustache\Tokenizer::FILTERS] = $filters;
                }
            }
            switch ($token[\Builderius\Mustache\Tokenizer::TYPE]) {
                case \Builderius\Mustache\Tokenizer::T_DELIM_CHANGE:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $this->clearStandaloneLines($nodes, $tokens);
                    break;
                case \Builderius\Mustache\Tokenizer::T_SECTION:
                case \Builderius\Mustache\Tokenizer::T_INVERTED:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $this->clearStandaloneLines($nodes, $tokens);
                    $nodes[] = $this->buildTree($tokens, $token);
                    break;
                case \Builderius\Mustache\Tokenizer::T_END_SECTION:
                    if (!isset($parent)) {
                        $msg = \sprintf('Unexpected closing tag: /%s on line %d', $token[\Builderius\Mustache\Tokenizer::NAME], $token[\Builderius\Mustache\Tokenizer::LINE]);
                        throw new \Builderius\Mustache\Exception\SyntaxException($msg, $token);
                    }
                    if ($token[\Builderius\Mustache\Tokenizer::NAME] !== $parent[\Builderius\Mustache\Tokenizer::NAME]) {
                        $msg = \sprintf('Nesting error: %s (on line %d) vs. %s (on line %d)', $parent[\Builderius\Mustache\Tokenizer::NAME], $parent[\Builderius\Mustache\Tokenizer::LINE], $token[\Builderius\Mustache\Tokenizer::NAME], $token[\Builderius\Mustache\Tokenizer::LINE]);
                        throw new \Builderius\Mustache\Exception\SyntaxException($msg, $token);
                    }
                    $this->clearStandaloneLines($nodes, $tokens);
                    $parent[\Builderius\Mustache\Tokenizer::END] = $token[\Builderius\Mustache\Tokenizer::INDEX];
                    $parent[\Builderius\Mustache\Tokenizer::NODES] = $nodes;
                    return $parent;
                case \Builderius\Mustache\Tokenizer::T_PARTIAL:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    //store the whitespace prefix for laters!
                    if ($indent = $this->clearStandaloneLines($nodes, $tokens)) {
                        $token[\Builderius\Mustache\Tokenizer::INDENT] = $indent[\Builderius\Mustache\Tokenizer::VALUE];
                    }
                    $nodes[] = $token;
                    break;
                case \Builderius\Mustache\Tokenizer::T_PARENT:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $nodes[] = $this->buildTree($tokens, $token);
                    break;
                case \Builderius\Mustache\Tokenizer::T_BLOCK_VAR:
                    if ($this->pragmaBlocks) {
                        // BLOCKS pragma is enabled, let's do this!
                        if (isset($parent) && $parent[\Builderius\Mustache\Tokenizer::TYPE] === \Builderius\Mustache\Tokenizer::T_PARENT) {
                            $token[\Builderius\Mustache\Tokenizer::TYPE] = \Builderius\Mustache\Tokenizer::T_BLOCK_ARG;
                        }
                        $this->clearStandaloneLines($nodes, $tokens);
                        $nodes[] = $this->buildTree($tokens, $token);
                    } else {
                        // pretend this was just a normal "escaped" token...
                        $token[\Builderius\Mustache\Tokenizer::TYPE] = \Builderius\Mustache\Tokenizer::T_ESCAPED;
                        // TODO: figure out how to figure out if there was a space after this dollar:
                        $token[\Builderius\Mustache\Tokenizer::NAME] = '$' . $token[\Builderius\Mustache\Tokenizer::NAME];
                        $nodes[] = $token;
                    }
                    break;
                case \Builderius\Mustache\Tokenizer::T_PRAGMA:
                    $this->enablePragma($token[\Builderius\Mustache\Tokenizer::NAME]);
                // no break
                case \Builderius\Mustache\Tokenizer::T_COMMENT:
                    $this->clearStandaloneLines($nodes, $tokens);
                    $nodes[] = $token;
                    break;
                default:
                    $nodes[] = $token;
                    break;
            }
        }
        if (isset($parent)) {
            $msg = \sprintf('Missing closing tag: %s opened on line %d', $parent[\Builderius\Mustache\Tokenizer::NAME], $parent[\Builderius\Mustache\Tokenizer::LINE]);
            throw new \Builderius\Mustache\Exception\SyntaxException($msg, $parent);
        }
        return $nodes;
    }
    /**
     * Clear standalone line tokens.
     *
     * Returns a whitespace token for indenting partials, if applicable.
     *
     * @param array $nodes  Parsed nodes
     * @param array $tokens Tokens to be parsed
     *
     * @return array|null Resulting indent token, if any
     */
    private function clearStandaloneLines(array &$nodes, array &$tokens)
    {
        if ($this->lineTokens > 1) {
            // this is the third or later node on this line, so it can't be standalone
            return;
        }
        $prev = null;
        if ($this->lineTokens === 1) {
            // this is the second node on this line, so it can't be standalone
            // unless the previous node is whitespace.
            if ($prev = \end($nodes)) {
                if (!$this->tokenIsWhitespace($prev)) {
                    return;
                }
            }
        }
        if ($next = \reset($tokens)) {
            // If we're on a new line, bail.
            if ($next[\Builderius\Mustache\Tokenizer::LINE] !== $this->lineNum) {
                return;
            }
            // If the next token isn't whitespace, bail.
            if (!$this->tokenIsWhitespace($next)) {
                return;
            }
            if (\count($tokens) !== 1) {
                // Unless it's the last token in the template, the next token
                // must end in newline for this to be standalone.
                if (\substr($next[\Builderius\Mustache\Tokenizer::VALUE], -1) !== "\n") {
                    return;
                }
            }
            // Discard the whitespace suffix
            \array_shift($tokens);
        }
        if ($prev) {
            // Return the whitespace prefix, if any
            return \array_pop($nodes);
        }
    }
    /**
     * Check whether token is a whitespace token.
     *
     * True if token type is T_TEXT and value is all whitespace characters.
     *
     * @param array $token
     *
     * @return bool True if token is a whitespace token
     */
    private function tokenIsWhitespace(array $token)
    {
        if ($token[\Builderius\Mustache\Tokenizer::TYPE] === \Builderius\Mustache\Tokenizer::T_TEXT) {
            return \preg_match('/^\\s*$/', $token[\Builderius\Mustache\Tokenizer::VALUE]);
        }
        return \false;
    }
    /**
     * Check whether a token is allowed inside a parent tag.
     *
     * @throws SyntaxException if an invalid token is found inside a parent tag
     *
     * @param array|null $parent
     * @param array      $token
     */
    private function checkIfTokenIsAllowedInParent($parent, array $token)
    {
        if (isset($parent) && $parent[\Builderius\Mustache\Tokenizer::TYPE] === \Builderius\Mustache\Tokenizer::T_PARENT) {
            throw new \Builderius\Mustache\Exception\SyntaxException('Illegal content in < parent tag', $token);
        }
    }
    /**
     * Split a tag name into name and filters.
     *
     * @param string $name
     *
     * @return array [Tag name, Array of filters]
     */
    private function getNameAndFilters($name)
    {
        $filters = \array_map('trim', \explode('|', $name));
        $name = \array_shift($filters);
        return array($name, $filters);
    }
    /**
     * Enable a pragma.
     *
     * @param string $name
     */
    private function enablePragma($name)
    {
        $this->pragmas[$name] = \true;
        switch ($name) {
            case \Builderius\Mustache\Engine::PRAGMA_BLOCKS:
                $this->pragmaBlocks = \true;
                break;
            case \Builderius\Mustache\Engine::PRAGMA_FILTERS:
                $this->pragmaFilters = \true;
                break;
        }
    }
}