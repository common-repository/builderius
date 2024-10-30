<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\GraphQL\Type\Definition\StringType;

class StringOrDataVarType extends StringType
{
    /**
     * {@inheritdoc}
     */
    public $name = 'StringOrDataVar';

    /**
     * {@inheritdoc}
     */
    public $description = 'string|"{{localDataVar}}"|"[[globalDataVar]]"';
}
