<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

class MixedOrDataVarType extends MixedType
{
    /**
     * {@inheritdoc}
     */
    public $name = 'MixedOrDataVar';

    /**
     * {@inheritdoc}
     */
    public $description = 'bool|int|float|string|array|"{{localDataVar}}"|"[[globalDataVar]]"';
}