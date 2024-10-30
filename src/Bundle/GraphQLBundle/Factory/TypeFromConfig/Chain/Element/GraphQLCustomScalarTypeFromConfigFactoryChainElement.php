<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLCustomScalarTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;

class GraphQLCustomScalarTypeFromConfigFactoryChainElement extends AbstractGraphQLTypeFromConfigFactoryChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(GraphQLTypeConfigInterface $config)
    {
        return $config instanceof GraphQLCustomScalarTypeConfigInterface;
    }

    /**
     * @inheritDoc
     */
    public function create(GraphQLTypeConfigInterface $config, array $dependsOnTypes = [])
    {
        // TODO: Implement create() method.
    }
}