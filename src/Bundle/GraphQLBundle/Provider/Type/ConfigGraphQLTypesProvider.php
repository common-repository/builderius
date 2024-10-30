<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\Type;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\GraphQLTypeFromConfigFactoryInterface;
use Builderius\Bundle\GraphQLBundle\Provider\TypeConfig\GraphQLTypeConfigsProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\GraphQL\Type\Definition\Type;

class ConfigGraphQLTypesProvider implements GraphQLTypesProviderInterface
{
    const CACHE_KEY = 'builderius_graphql_types_form_configs';

    /**
     * @var Type[]
     */
    private $types = [];

    /**
     * @var GraphQLTypeConfigsProviderInterface
     */
    private $typeConfigsProvider;

    /**
     * @var GraphQLTypeFromConfigFactoryInterface
     */
    private $typeFromConfigFactory;

    /**
     * @var GraphQLTypesProviderInterface
     */
    private $standardTypesProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param GraphQLTypeConfigsProviderInterface $typeConfigsProvider
     * @param GraphQLTypeFromConfigFactoryInterface $typeFromConfigFactory
     * @param GraphQLTypesProviderInterface $standardTypesProvider
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        GraphQLTypeConfigsProviderInterface $typeConfigsProvider,
        GraphQLTypeFromConfigFactoryInterface $typeFromConfigFactory,
        GraphQLTypesProviderInterface $standardTypesProvider,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->typeConfigsProvider = $typeConfigsProvider;
        $this->typeFromConfigFactory = $typeFromConfigFactory;
        $this->standardTypesProvider = $standardTypesProvider;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getTypes()
    {
        $types = $this->cache->get(self::CACHE_KEY);
        if (false === $types) {
            foreach ($this->typeConfigsProvider->getTypeConfigs() as $typeConfig) {
                $this->generateType($typeConfig);
            }
            $types = $this->types;
            $this->cache->set(self::CACHE_KEY, $types);
        }

        return $types;
    }

    /**
     * @param GraphQLTypeConfigInterface $typeConfig
     * @param array $parentTypesNames
     * @throws \Exception
     */
    private function generateType(GraphQLTypeConfigInterface $typeConfig, $parentTypesNames = [])
    {
        $typeConfigName = $typeConfig->getName();
        if (!$typeConfig instanceof GraphQLObjectTypeConfigInterface) {
            $this->types[$typeConfigName] = $this->typeFromConfigFactory->createType($typeConfig);
        } else {
            $dependsOnTypes = [];
            foreach ($typeConfig->getInterfaces() as $interface) {
                $interfaceTypeName = $interface->getName();
                if ($interfaceTypeName !== $typeConfigName && !in_array($interfaceTypeName, $parentTypesNames)) {
                    $interfaceType = null;
                    if (!$interfaceType = $this->standardTypesProvider->getType($interfaceTypeName)) {
                        $interfaceType = isset($this->types[$interfaceTypeName]) ? $this->types[$interfaceTypeName] : null;
                    }
                    if (null === $interfaceType) {
                        if (!$this->typeConfigsProvider->hasTypeConfig($interfaceTypeName)) {
                            throw new \Exception(sprintf('Missing %s GraphQL type config', $interfaceTypeName));
                        } else {
                            $parentTypesNames[$typeConfigName] = $typeConfigName;
                            $this->generateType($this->typeConfigsProvider->getTypeConfig($interfaceTypeName), $parentTypesNames);
                            $interfaceType = $this->types[$interfaceTypeName];
                        }
                    }
                    $dependsOnTypes[$interfaceTypeName] = $interfaceType;
                }
            }
            foreach ($typeConfig->getFields() as $field) {
                $fieldTypeName = str_replace('!', '', str_replace(']', '', str_replace('[', '', $field->getType())));
                if ($fieldTypeName !== $typeConfigName && !in_array($fieldTypeName, $parentTypesNames) && !array_key_exists($fieldTypeName, $dependsOnTypes)) {
                    $fieldType = null;
                    if (!$fieldType = $this->standardTypesProvider->getType($fieldTypeName)) {
                        $fieldType = isset($this->types[$fieldTypeName]) ? $this->types[$fieldTypeName] : null;
                    }
                    if (null === $fieldType) {
                        if (!$this->typeConfigsProvider->hasTypeConfig($fieldTypeName)) {
                            throw new \Exception(sprintf('Missing %s GraphQL type config', $fieldTypeName));
                        } else {
                            $parentTypesNames[$typeConfigName] = $typeConfigName;
                            $this->generateType($this->typeConfigsProvider->getTypeConfig($fieldTypeName), $parentTypesNames);
                            $fieldType = $this->types[$fieldTypeName];
                        }
                    }
                    $dependsOnTypes[$fieldTypeName] = $fieldType;
                    foreach ($field->getArguments() as $argument) {
                        $argumentTypeName = str_replace('!', '', str_replace(']', '', str_replace('[', '', $argument->getType())));
                        if (!array_key_exists($argumentTypeName, $dependsOnTypes)) {
                            $argumentType = null;
                            if (!$argumentType = $this->standardTypesProvider->getType($argumentTypeName)) {
                                $argumentType = isset($this->types[$argumentTypeName]) ? $this->types[$argumentTypeName] : null;
                            }
                            if (null === $argumentType) {
                                if (!$this->typeConfigsProvider->hasTypeConfig($argumentTypeName)) {
                                    throw new \Exception(sprintf('Missing %s GraphQL type config', $argumentTypeName));
                                } else {
                                    $parentTypesNames[$typeConfigName] = $typeConfigName;
                                    $this->generateType($this->typeConfigsProvider->getTypeConfig($argumentTypeName), $parentTypesNames);
                                    $argumentType = $this->types[$argumentTypeName];
                                }
                            }
                            $dependsOnTypes[$argumentTypeName] = $argumentType;
                        }
                    }
                }
            }
            $this->types[$typeConfig->getName()] = $this->typeFromConfigFactory->createType($typeConfig, $dependsOnTypes);
        }
    }

    /**
     * @inheritDoc
     */
    public function getType($name)
    {
        return $this->hasType($name) ? $this->getTypes()[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function hasType($name)
    {
        return isset($this->getTypes()[$name]);
    }
}