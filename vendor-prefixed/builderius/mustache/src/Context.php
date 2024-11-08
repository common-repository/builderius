<?php

namespace Builderius\Mustache;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Mustache\Exception\InvalidArgumentException;
/**
 * Mustache Template rendering Context.
 */
class Context
{
    private $stack = array();
    private $blockStack = array();
    private $globalContext = array();
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;
    /**
     * Mustache rendering Context constructor.
     *
     * @param mixed $context Default rendering context (default: null)
     */
    public function __construct($context = null)
    {
        if ($context !== null) {
            $this->stack = array($context);
        }
    }
    /**
     * @param ExpressionLanguage $expressionLanguage
     * @return $this
     */
    public function setExpressionLanguage(\Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
        return $this;
    }
    /**
     * @param array $globalContext
     * @return $this
     */
    public function setGlobalContext(array $globalContext = null)
    {
        $this->globalContext = $globalContext;
        return $this;
    }
    /**
     * Push a new Context frame onto the stack.
     *
     * @param mixed $value Object or array to use for context
     */
    public function push($value)
    {
        \array_push($this->stack, $value);
    }
    /**
     * Push a new Context frame onto the block context stack.
     *
     * @param mixed $value Object or array to use for block context
     */
    public function pushBlockContext($value)
    {
        \array_push($this->blockStack, $value);
    }
    /**
     * Pop the last Context frame from the stack.
     *
     * @return mixed Last Context frame (object or array)
     */
    public function pop()
    {
        return \array_pop($this->stack);
    }
    /**
     * Pop the last block Context frame from the stack.
     *
     * @return mixed Last block Context frame (object or array)
     */
    public function popBlockContext()
    {
        return \array_pop($this->blockStack);
    }
    /**
     * Get the last Context frame.
     *
     * @return mixed Last Context frame (object or array)
     */
    public function last()
    {
        return \end($this->stack);
    }
    /**
     * Find a variable in the Context stack.
     *
     * Starting with the last Context frame (the context of the innermost section), and working back to the top-level
     * rendering context, look for a variable with the given name:
     *
     *  * If the Context frame is an associative array which contains the key $id, returns the value of that element.
     *  * If the Context frame is an object, this will check first for a public method, then a public property named
     *    $id. Failing both of these, it will try `__isset` and `__get` magic methods.
     *  * If a value named $id is not found in any Context frame, returns an empty string.
     *
     * @param string $id Variable name
     *
     * @return mixed Variable value, or '' if not found
     */
    public function find($id)
    {
        return $this->findVariableInStack($id, $this->stack);
    }
    /**
     * Find a 'dot notation' variable in the Context stack.
     *
     * Note that dot notation traversal bubbles through scope differently than the regular find method. After finding
     * the initial chunk of the dotted name, each subsequent chunk is searched for only within the value of the previous
     * result. For example, given the following context stack:
     *
     *     $data = array(
     *         'name' => 'Fred',
     *         'child' => array(
     *             'name' => 'Bob'
     *         ),
     *     );
     *
     * ... and the Mustache following template:
     *
     *     {{ child.name }}
     *
     * ... the `name` value is only searched for within the `child` value of the global Context, not within parent
     * Context frames.
     *
     * @param string $id Dotted variable selector
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findDot($id)
    {
        return $this->findVariableInStack($id, $this->stack);
    }
    /**
     * Find an 'anchored dot notation' variable in the Context stack.
     *
     * This is the same as findDot(), except it looks in the top of the context
     * stack for the first value, rather than searching the whole context stack
     * and starting from there.
     *
     * @see Context::findDot
     *
     * @throws InvalidArgumentException if given an invalid anchored dot $id
     *
     * @param string $id Dotted variable selector
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findAnchoredDot($id)
    {
        return $this->findVariableInStack($id, $this->stack);
    }
    /**
     * Find an argument in the block context stack.
     *
     * @param string $id
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findInBlock($id)
    {
        foreach ($this->blockStack as $context) {
            if (\array_key_exists($id, $context)) {
                return $context[$id];
            }
        }
        return '';
    }
    /**
     * Helper function to find a variable in the Context stack.
     *
     * @see Context::find
     *
     * @param string $id    Variable name
     * @param array  $stack Context stack
     *
     * @return mixed Variable value, or '' if not found
     */
    private function findVariableInStack($id, array $stack)
    {
        $id = rawurldecode(htmlspecialchars_decode($id));
        $this->stack = $stack;
        $frame = $this->last('.');
        try {
            if (is_array($this->globalContext) && is_array($frame)) {
                $frame = array_merge($this->globalContext, $frame);
            }
            return $this->expressionLanguage->evaluate($id, $frame);
        } catch (\Exception|\Error $e) {
            return '';
        }
    }
}
