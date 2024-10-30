<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Node;

use Builderius\Symfony\Component\ExpressionLanguage\Node\NameNode;
use Builderius\Symfony\Component\ExpressionLanguage\Node\Node;
use Builderius\Symfony\Component\ExpressionLanguage\Compiler;
use Builderius\Bundle\ExpressionLanguageBundle\SafeCallable;

/**
 * @internal
 */
class ArrowFuncNode extends Node
{
    /**
     * @var SafeCallable
     */
    private static $noopSafeCallable;

    /**
     * @param NameNode[] $parameters
     * @param Node|null  $body
     */
    public function __construct(array $parameters, Node $body = null)
    {
        parent::__construct(
            array(
                'parameters' => $parameters,
                'body' => $body,
            )
        );

        if (!self::$noopSafeCallable) {
            self::$noopSafeCallable = new SafeCallable(function () {
            });
        }
    }

    public function compile(Compiler $compiler)
    {
        $arguments = array();

        foreach ($this->nodes['parameters'] as $parameterNode) {
            $arguments[] = $compiler->subcompile($parameterNode);
        }

        $compiler->raw(
            sprintf(
                'function (%s) { return %s; }',
                implode(', ', $arguments),
                $this->nodes['body'] ? $compiler->subcompile($this->nodes['body']) : 'null'
            )
        );
    }

    public function evaluate($functions, $values)
    {
        /** @var Node|null $bodyNode */
        $bodyNode = $this->nodes['body'];

        if (!$bodyNode) {
            return self::$noopSafeCallable;
        }

        $paramNames = array();

        foreach ($this->nodes['parameters'] as $parameterNode) {
            /** @var NameNode $parameterNode */
            $nodeData = $parameterNode->toArray();
            $paramNames[] = $nodeData[0];
        }

        return new SafeCallable(
            function () use ($functions, $paramNames, $bodyNode, $values) {
                $passedValues = $this->arrayCombine($paramNames, func_get_args());
                foreach ($passedValues as $key => $value) {
                    $values[$key] = $value;
                }

                return $bodyNode->evaluate($functions, $values);
            }
        );
    }

    /**
     * @param array $keys
     * @param array $values
     * @return array
     */
    private function arrayCombine(array $keys, array $values) {
        $newArray = [];
        if (empty($keys) || empty($values)) {
            return $newArray;
        }
        if (count($keys) === 2 && count($values) === 1) {
            foreach ($values as $key => $value) {
                $newArray[$keys[0]] = $value;
                $newArray[$keys[1]] = $key;
            }

            return $newArray;
        }
        foreach ($keys as $i => $key) {
            if (isset($values[$i])) {
                $newArray[$key] = $values[$i];
            }
        }

        return $newArray;
    }

    public function toArray()
    {
        $array = array();

        foreach ($this->nodes['parameters'] as $node) {
            $array[] = ', ';
            $array[] = $node;
        }
        $array[0] = '(';
        $array[] = ') -> {';
        if ($this->nodes['body']) {
            $array[] = $this->nodes['body'];
        }
        $array[] = '}';

        return $array;
    }
}