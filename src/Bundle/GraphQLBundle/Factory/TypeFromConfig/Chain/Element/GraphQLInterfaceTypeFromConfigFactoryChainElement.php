<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLInterfaceTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLInterfaceTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Type\RootTypeInterface;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class GraphQLInterfaceTypeFromConfigFactoryChainElement extends AbstractGraphQLObjectTypeFromConfigFactoryChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(GraphQLTypeConfigInterface $config)
    {
        return $config instanceof GraphQLInterfaceTypeConfigInterface && !$config instanceof RootTypeInterface;
    }

    /**
     * @inheritDoc
     */
    public function create(GraphQLTypeConfigInterface $config, array $dependsOnTypes = [])
    {
        /** @var GraphQLInterfaceTypeConfigInterface $config */
        $params = [
            GraphQLInterfaceTypeConfig::NAME_FIELD => $config->getName(),
            GraphQLInterfaceTypeConfig::DESCRIPTION_FIELD => $config->getDescription(),
            GraphQLInterfaceTypeConfig::FIELDS_FIELD => function () use ($config, $dependsOnTypes) {
                return $this->generateFields(
                    $config->getName(),
                    $config->getFields(),
                    [],
                    $dependsOnTypes
                );
            }
        ];
        if ($config->getTypeResolver()) {
            $params[GraphQLInterfaceTypeConfig::TYPE_RESOLVER_FIELD] = function($value, $context, ResolveInfo $info) use ($config){
                return $config->getTypeResolver()->resolve($value, $context, $info);
            };
        }

        return new InterfaceType($params);
    }
}