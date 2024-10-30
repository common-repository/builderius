<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Type\Definition\InputObjectType;

class FloatWithOperatorType extends InputObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $config = [
            'name' => 'FloatWithOperator',
            'fields' => function() {
                return [
                    'operator' => ['type' => 'Operator!'],
                    'value' => ['type' => 'Float!'],
                ];
            }
        ];

        parent::__construct($config);
    }
}