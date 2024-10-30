<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\GraphQL\Type\Definition\EnumType;

class GraphQLEnumTypeFromConfigFactoryChainElement extends AbstractGraphQLTypeFromConfigFactoryChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(GraphQLTypeConfigInterface $config)
    {
        return $config instanceof GraphQLEnumTypeConfigInterface;
    }

    /**
     * @inheritDoc
     */
    public function create(GraphQLTypeConfigInterface $config, array $dependsOnTypes = [])
    {
        /** @var GraphQLEnumTypeConfigInterface $config */
        $params = [
            GraphQLEnumTypeConfig::NAME_FIELD => $config->getName(),
            GraphQLEnumTypeConfig::DESCRIPTION_FIELD => $config->getDescription(),
            GraphQLEnumTypeConfig::VALUES_FIELD => $this->generateValues($config->getValues())
        ];

        return new EnumType($params);
    }

    /**
     * @param GraphQLEnumValueConfigInterface[] $values
     * @return array
     */
    protected function generateValues(array $values)
    {
        $params = [];
        foreach ($values as $value) {
            $params[$value->getName()] = [
                GraphQLEnumValueConfig::DESCRIPTION_FIELD => $value->getDescription(),
                GraphQLEnumValueConfig::VALUE_FIELD => $value->getValue()
            ];
        }

        return $params;
    }
}