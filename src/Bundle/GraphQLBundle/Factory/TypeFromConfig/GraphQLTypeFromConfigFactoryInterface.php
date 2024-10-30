<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\GraphQL\Type\Definition\Type;

interface GraphQLTypeFromConfigFactoryInterface
{
    /**
     * @param GraphQLTypeConfigInterface $typeConfig
     * @param Type[] $dependsOnTypes
     * @return Type
     */
    public function createType(GraphQLTypeConfigInterface $typeConfig, array $dependsOnTypes = []);
}