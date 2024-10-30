<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\Type;

use Builderius\GraphQL\Type\Definition\Type;

interface GraphQLTypesProviderInterface
{
    /**
     * @return Type[]
     */
    public function getTypes();

    /**
     * @param string $name
     * @return Type
     */
    public function getType($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasType($name);
}