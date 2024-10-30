<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Utils\AST;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Type\Definition\ScalarType;

class MixedType extends ScalarType
{
    /**
     * {@inheritdoc }
     */
    public $name = 'Mixed';

    /**
     * @inheritDoc
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        return AST::valueFromASTUntyped($valueNode);
    }
}