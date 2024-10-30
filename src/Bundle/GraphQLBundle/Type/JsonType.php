<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\ObjectValueNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Type\Definition\ScalarType;

class JsonType extends ScalarType
{
    /**
     * {@inheritdoc }
     */
    public $name = 'Json';

    /**
     * {@inheritdoc }
     */
    public $description =
        'The `JSON` scalar type represents JSON values as specified by
        [ECMA-404](http://www.ecma-international.org/publications/files/ECMA-ST/ECMA-404.pdf).';

    /**
     * {@inheritdoc }
     */
    public function __construct(?string $name = null)
    {
        if ($name) {
            $this->name = $name;
        }
        parent::__construct();
    }

    /**
     * {@inheritdoc }
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc }
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc }
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        switch ($valueNode) {
            case ($valueNode instanceof StringValueNode):
            case ($valueNode instanceof BooleanValueNode):
                return $valueNode->value;
            case ($valueNode instanceof IntValueNode):
            case ($valueNode instanceof FloatValueNode):
                return floatval($valueNode->value);
            case ($valueNode instanceof ObjectValueNode): {
                $value = [];
                foreach ($valueNode->fields as $field) {
                    $value[$field->name->value] = $this->parseLiteral($field->value);
                }
                return $value;
            }
            case ($valueNode instanceof ListValueNode):
                return array_map([$this, 'parseLiteral'], $valueNode->values);
            default:
                return null;
        }
    }
}