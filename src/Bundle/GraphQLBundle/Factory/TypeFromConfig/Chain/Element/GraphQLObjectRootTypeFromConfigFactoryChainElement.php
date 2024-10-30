<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectRootTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectRootTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Type\ObjectRootType;
use Builderius\Bundle\GraphQLBundle\Type\RootTypeInterface;

class GraphQLObjectRootTypeFromConfigFactoryChainElement extends AbstractGraphQLObjectTypeFromConfigFactoryChainElement
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
        return $config instanceof GraphQLObjectTypeConfigInterface && $config instanceof RootTypeInterface;
    }

    /**
     * @inheritDoc
     */
    public function create(GraphQLTypeConfigInterface $config, array $dependsOnTypes = [])
    {
        /** @var GraphQLObjectRootTypeConfigInterface $config */
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
            GraphQLObjectRootTypeConfig::NAME_FIELD => $config->getName(),
            GraphQLObjectRootTypeConfig::DESCRIPTION_FIELD => $config->getDescription(),
            GraphQLObjectRootTypeConfig::INTERFACES_FIELD => function() use ($interfaceConfigs, $typesProvider) {
                $interfaceTypes = [];
                foreach ($interfaceConfigs as $interfaceConfig) {
                    $interfaceTypes[] = $typesProvider->getType($interfaceConfig->getName());
                }

                return $interfaceTypes;
            },
            GraphQLObjectRootTypeConfig::FIELDS_FIELD =>  function() use ($config, $fields, $interfaceConfigs, $dependsOnTypes){
                return $this->generateFields(
                    $config->getName(),
                    $fields,
                    $interfaceConfigs,
                    $dependsOnTypes
                );
            },
            GraphQLObjectRootTypeConfig::APPLIED_TO_ALL_TEMPLATE_TYPES => $config->isAppliedToAllTemplateTypes(),
            GraphQLObjectRootTypeConfig::TEMPLATE_TYPE => $config->getTemplateType(),
            GraphQLObjectRootTypeConfig::ROOT_DATA_PROVIDER => $config->getRootDataProvider()

        ];

        return new ObjectRootType($params);
    }
}