<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Type\Definition\InputObjectType;

class BooleanWithOperatorType extends InputObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $config = [
            'name' => 'BooleanWithOperator',
            'fields' => function() {
                return [
                    'operator' => ['type' => 'Operator!'],
                    'value' => ['type' => 'Boolean!'],
                ];
            }
        ];

        parent::__construct($config);
    }
}