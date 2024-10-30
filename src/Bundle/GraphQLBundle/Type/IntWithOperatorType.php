<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Type\Definition\InputObjectType;

class IntWithOperatorType extends InputObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $config = [
            'name' => 'IntWithOperator',
            'fields' => function() {
                return [
                    'operator' => ['type' => 'Operator!'],
                    'value' => ['type' => 'Int!'],
                ];
            }
        ];

        parent::__construct($config);
    }
}