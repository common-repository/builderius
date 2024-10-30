<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLInputObjectTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLInputObjectTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Type\RootTypeInterface;
use Builderius\GraphQL\Type\Definition\InputObjectType;

class GraphQLInputObjectTypeFromConfigFactoryChainElement extends AbstractGraphQLObjectTypeFromConfigFactoryChainElement
{
    /**
     * @var GraphQLTypesProviderInterface
     */
    private $typesProvider;

    /**
     * @param GraphQLTypesProviderInterface $typesProvider
     * @return $this
     */
    public function setTypesProvider(GraphQLTypesProviderInterface $typesProvider)
    {
        $this->typesProvider = $typesProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(GraphQLTypeConfigInterface $config)
    {
        return $config instanceof GraphQLInputObjectTypeConfigInterface && !$config instanceof RootTypeInterface;
    }

    /**
     * @inheritDoc
     */
    public function create(GraphQLTypeConfigInterface $config, array $dependsOnTypes = [])
    {
        /** @var GraphQLInputObjectTypeConfigInterface $config */
        $interfaceConfigs = $config->getInterfaces();
        $typesProvider = $this->typesProvider;
        $fields = $config->getFields();
        foreach ($interfaceConfigs as $interfaceConfig) {
            $interfaceFields = $interfaceConfig->getFields();
            foreach ($interfaceFields as $interfaceField) {
                if (!in_array($interfaceField, $fields)) {
                    $fields[$interfaceField->getName()] = $interfaceField;
                }
            }
        }
        $params = [
            GraphQLInputObjectTypeConfig::NAME_FIELD => $config->getName(),
            GraphQLInputObjectTypeConfig::DESCRIPTION_FIELD => $config->getDescription(),
            GraphQLInputObjectTypeConfig::INTERFACES_FIELD => function() use ($interfaceConfigs, $typesProvider) {
                $interfaceTypes = [];
                foreach ($interfaceConfigs as $interfaceConfig) {
                    $interfaceTypes[] = $typesProvider->getType($interfaceConfig->getName());
                }

                return $interfaceTypes;
            },
            GraphQLInputObjectTypeConfig::FIELDS_FIELD =>  function() use ($config, $fields, $interfaceConfigs, $dependsOnTypes){
                return $this->generateFields(
                    $config->getName(),
                    $fields,
                    $interfaceConfigs,
                    $dependsOnTypes
                );
            }
        ];

        return new InputObjectType($params);
    }
}