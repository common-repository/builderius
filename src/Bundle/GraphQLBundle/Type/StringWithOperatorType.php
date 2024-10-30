<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Type\Definition\InputObjectType;

class StringWithOperatorType extends InputObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $config = [
            'name' => 'StringWithOperator',
            'fields' => function() {
                return [
                    'operator' => ['type' => 'Operator!'],
                    'value' => ['type' => 'String!'],
                ];
            }
        ];

        parent::__construct($config);
    }
}