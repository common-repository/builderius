<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Type\Definition\ObjectType;

class MixedWithOperatorType extends ObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $config = [
            'name' => 'MixedWithOperator',
            'fields' => function() {
                return [
                    'operator' => ['type' => 'Operator!'],
                    'value' => ['type' => 'Mixed!'],
                ];
            }
        ];

        parent::__construct($config);
    }
}