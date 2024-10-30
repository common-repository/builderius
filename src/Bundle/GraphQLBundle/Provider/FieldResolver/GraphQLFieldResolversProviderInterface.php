<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\FieldResolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;

interface GraphQLFieldResolversProviderInterface
{
    /**
     * @param string $typeName
     * @param string $fieldName
     * @return GraphQLFieldResolverInterface[]
     */
    public function getResolvers($typeName, $fieldName);

    /**
     * @param string $typeName
     * @param string $fieldName
     * @return bool
     */
    public function hasResolvers($typeName, $fieldName);
}