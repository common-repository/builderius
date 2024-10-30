<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\GraphQLTypeFromConfigFactoryInterface;

abstract class AbstractGraphQLTypeFromConfigFactoryChainElement implements
    GraphQLTypeFromConfigFactoryInterface,
    GraphQLTypeFromConfigFactoryChainElementInterface
{
    /**
     * @var GraphQLTypeFromConfigFactoryChainElementInterface|null
     */
    private $successor;

    /**
     * @inheritDoc
     */
    public function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function setSuccessor(GraphQLTypeFromConfigFactoryChainElementInterface $successor)
    {
        $this->successor = $successor;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createType(GraphQLTypeConfigInterface $typeConfig, array $dependsOnTypes = [])
    {
        if ($this->isApplicable($typeConfig)) {
            return $this->create($typeConfig, $dependsOnTypes);
        } elseif ($this->getSuccessor()) {
            return $this->getSuccessor()->createType($typeConfig, $dependsOnTypes);
        }

        return null;
    }
}