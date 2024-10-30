<?php

namespace Builderius\Bundle\GraphQLBundle\Factory\TypeFromConfig\Chain\Element;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldArgumentConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldArgumentConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLInterfaceTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Provider\FieldResolver\GraphQLFieldResolversProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;

abstract class AbstractGraphQLObjectTypeFromConfigFactoryChainElement extends AbstractGraphQLTypeFromConfigFactoryChainElement
{
    /**
     * @var GraphQLFieldResolversProviderInterface
     */
    protected $fieldResolversProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param GraphQLFieldResolversProviderInterface $fieldResolversProvider
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        GraphQLFieldResolversProviderInterface $fieldResolversProvider,
        BuilderiusRuntimeObjectCache           $cache
    ) {
        $this->fieldResolversProvider = $fieldResolversProvider;
        $this->cache = $cache;
    }

    /**
     * @param string $typeName
     * @param GraphQLFieldConfigInterface[] $fields
     * @param GraphQLInterfaceTypeConfigInterface[] $interfaces
     * @param Type[] $dependsOnTypes
     * @return array
     */
    protected function generateFields($typeName, array $fields, array $interfaces = [], array $dependsOnTypes = [])
    {
        $params = [];
        foreach ($fields as $field) {
            if ($field instanceof ConditionAwareInterface && $field->hasConditions()) {
                $evaluated = true;
                foreach ($field->getConditions() as $condition) {
                    if ($condition->evaluate() === false) {
                        $evaluated = false;
                        break;
                    }
                }
                if (!$evaluated) {
                    continue;
                }
                $params[] = $this->processField($typeName, $field, $interfaces, $dependsOnTypes);
            } else {
                $params[] = $this->processField($typeName, $field, $interfaces, $dependsOnTypes);
            }
        }

        return $params;
    }

    /**
     * @param string $typeName
     * @param GraphQLFieldConfigInterface $field
     * @param GraphQLInterfaceTypeConfigInterface[] $interfaces
     * @param Type[] $dependsOnTypes
     * @return array
     */
    private function processField($typeName, GraphQLFieldConfigInterface $field, array $interfaces = [], array $dependsOnTypes = [])
    {
        $fieldName = $field->getName();
        $fieldParams = [
            GraphQLFieldConfig::NAME_FIELD => $fieldName,
            GraphQLFieldConfig::DESCRIPTION_FIELD => $field->getDescription(),
            GraphQLFieldConfig::TYPE_FIELD => function () use ($field, $dependsOnTypes) {
                return $this->generateType($field->getType(), $dependsOnTypes);
            },
            GraphQLFieldConfig::ARGUMENTS_FIELD => $this->generateArguments($field->getArguments(), $dependsOnTypes)
        ];
        $hasInterfaceFieldResolver = false;
        foreach ($interfaces as $interface) {
            if ($this->fieldResolversProvider->hasResolvers($interface->getName(), $fieldName)) {
                $hasInterfaceFieldResolver = true;
                break;
            }
        }
        if ($hasInterfaceFieldResolver || $this->fieldResolversProvider->hasResolvers($typeName, $fieldName)) {
            $fieldResolversProvider = $this->fieldResolversProvider;
            $fieldParams[GraphQLFieldConfig::RESOLVER_FIELD] = function(
                $objectValue,
                $args,
                $context,
                ResolveInfo $info
            ) use ($typeName, $fieldName, $interfaces, $fieldResolversProvider) {
                foreach ($interfaces as $interface) {
                    foreach ($fieldResolversProvider->getResolvers($interface->getName(), $fieldName) as $fieldResolver) {
                        if ($fieldResolver->isApplicable($objectValue, $args, $context, $info)) {
                            return $fieldResolver->resolve($objectValue, $args, $context, $info);
                        }
                    }
                }
                foreach ($fieldResolversProvider->getResolvers($typeName, $fieldName) as $fieldResolver) {
                    if ($fieldResolver->isApplicable($objectValue, $args, $context, $info)) {
                        return $fieldResolver->resolve($objectValue, $args, $context, $info);
                    }
                }

                return null;
            };
        }

        return $fieldParams;
    }

    /**
     * @param GraphQLFieldArgumentConfigInterface[] $arguments
     * @param Type[] $dependsOnTypes
     * @return array
     */
    protected function generateArguments(array $arguments, array $dependsOnTypes = [])
    {
        $params = [];
        foreach ($arguments as $argument) {
            $argumentParams = [
                GraphQLFieldArgumentConfig::NAME_FIELD => $argument->getName(),
                GraphQLFieldArgumentConfig::TYPE_FIELD => function () use ($argument, $dependsOnTypes) {
                    return $this->generateType($argument->getType(), $dependsOnTypes);
                },
                GraphQLFieldArgumentConfig::DESCRIPTION_FIELD => $argument->getDescription()
            ];
            if ($argument->getDefaultValue()) {
                $argumentParams[GraphQLFieldArgumentConfig::DEFAULT_VALUE_FIELD] = $argument->getDefaultValue();
            }
            $params[] = $argumentParams;
        }

        return $params;
    }

    /**
     * @param $name
     * @param array $dependsOnTypes
     * @return Type
     */
    protected function generateType($name, array $dependsOnTypes = [])
    {
        $nonNull = substr($name, -1) === '!' ? true : false;
        if ($nonNull) {
            $name = substr($name, 0, -1);
        }
        $listOf = (substr($name, 0, 1) === '[' && substr($name, -1) === ']') ? true : false;
        if ($listOf) {
            $name = substr(substr($name, 0, -1), 1);
        }
        $type = null;
        if (isset($dependsOnTypes[$name])) {
            $type = $dependsOnTypes[$name];
        }
        if (null === $type) {
            $types = $this->cache->get('builderius_graphql_types');
            if (false !== $types) {
                $type = $types[$name];
            }
        }

        return $nonNull ? new NonNull($type) : ($listOf ? new ListOfType($type) : $type);
    }
}