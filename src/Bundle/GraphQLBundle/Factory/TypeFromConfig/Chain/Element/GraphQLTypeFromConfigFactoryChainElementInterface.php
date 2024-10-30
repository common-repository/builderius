<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\GraphQL\Type\Definition\Type;

interface GraphQLTypeFromConfigFactoryChainElementInterface
{
    /**
     * @return GraphQLTypeFromConfigFactoryChainElementInterface
     */
    public function getSuccessor();

    /**
     * @param GraphQLTypeFromConfigFactoryChainElementInterface $successor
     * @return $this
     */
    public function setSuccessor(GraphQLTypeFromConfigFactoryChainElementInterface $successor);

    /**
     * @param GraphQLTypeConfigInterface $config
     * @return bool
     */
    public function isApplicable(GraphQLTypeConfigInterface $config);

    /**
     * @param GraphQLTypeConfigInterface $config
     * @param Type[] $dependsOnTypes
     * @return Type
     */
    public function create(GraphQLTypeConfigInterface $config, array $dependsOnTypes = []);
}