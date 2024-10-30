<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Node;

use Builderius\Symfony\Component\ExpressionLanguage\Node\ArrayNode;
use Builderius\Symfony\Component\ExpressionLanguage\Node\Node;
use Builderius\Symfony\Component\ExpressionLanguage\Compiler;
use Builderius\Symfony\Component\PropertyAccess\PropertyAccess;

class GetAttrNode extends Node
{
    const PROPERTY_CALL = 1;
    const METHOD_CALL = 2;
    const ARRAY_CALL = 3;

    public function __construct(Node $node, Node $attribute, ArrayNode $arguments, int $type)
    {
        parent::__construct(['node' => $node, 'attribute' => $attribute, 'arguments' => $arguments], ['type' => $type]);
    }

    public function compile(Compiler $compiler)
    {
        switch ($this->attributes['type']) {
            case self::PROPERTY_CALL:
                $compiler->compile($this->nodes['node'])->raw('->')->raw($this->nodes['attribute']->attributes['value']);
                break;
            case self::METHOD_CALL:
                $compiler->compile($this->nodes['node'])->raw('->')->raw($this->nodes['attribute']->attributes['value'])->raw('(')->compile($this->nodes['arguments'])->raw(')');
                break;
            case self::ARRAY_CALL:
                $compiler->compile($this->nodes['node'])->raw('[')->compile($this->nodes['attribute'])->raw(']');
                break;
        }
    }

    public function evaluate(array $functions, array $values)
    {
        switch ($this->attributes['type']) {
            case self::PROPERTY_CALL:
                $obj = (object)$this->nodes['node']->evaluate($functions, $values);
                $property = $this->nodes['attribute']->attributes['value'];
                if (!\is_object($obj)) {
                    $obj = (object)[$property => null];
                    //throw new \RuntimeException('Unable to get a property on a non-object.');
                }
                if (!property_exists($obj, $property)) {
                    $obj->$property = null;
                }
                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                return $propertyAccessor->getValue($obj, $property);
            case self::METHOD_CALL:
                $obj = (object)$this->nodes['node']->evaluate($functions, $values);
                if (!\is_object($obj)) {
                    throw new \RuntimeException('Unable to get a property on a non-object.');
                }
                if (!\is_callable($toCall = [$obj, $this->nodes['attribute']->attributes['value']])) {
                    throw new \RuntimeException(\sprintf('Unable to call method "%s" of object "%s".', $this->nodes['attribute']->attributes['value'], \get_class($obj)));
                }
                return $toCall(...\array_values($this->nodes['arguments']->evaluate($functions, $values)));
            case self::ARRAY_CALL:
                $array = $this->nodes['node']->evaluate($functions, $values);
                $key = $this->nodes['attribute']->evaluate($functions, $values);
                if (is_array($array) || $array instanceof \ArrayAccess) {
                    return $array[$key];
                } elseif (is_object($array)) {
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    return $propertyAccessor->getValue($array, $key);
                } else {
                    throw new \RuntimeException('Unable to get an item on a non-array.');
                }
        }
    }

    public function toArray()
    {
        switch ($this->attributes['type']) {
            case self::PROPERTY_CALL:
                return [$this->nodes['node'], '.', $this->nodes['attribute']];
            case self::METHOD_CALL:
                return [$this->nodes['node'], '.', $this->nodes['attribute'], '(', $this->nodes['arguments'], ')'];
            case self::ARRAY_CALL:
                return [$this->nodes['node'], '[', $this->nodes['attribute'], ']'];
        }
    }
}
