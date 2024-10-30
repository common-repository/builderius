<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectRootTypeConfig;
use Builderius\Bundle\GraphQLBundle\Provider\RootData\BuilderiusGraphQLRootDataProviderInterface;
use Builderius\GraphQL\Type\Definition\ObjectType;

class ObjectRootType extends ObjectType implements RootTypeInterface
{
    /**
     * @inheritDoc
     */
    public function isAppliedToAllTemplateTypes()
    {
        return isset($this->config[GraphQLObjectRootTypeConfig::APPLIED_TO_ALL_TEMPLATE_TYPES]) ?
            $this->config[GraphQLObjectRootTypeConfig::APPLIED_TO_ALL_TEMPLATE_TYPES] : false;
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTemplateTypes($appliedToAll = false)
    {
        $this->config[GraphQLObjectRootTypeConfig::APPLIED_TO_ALL_TEMPLATE_TYPES] = $appliedToAll;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getTemplateType()
    {
        return isset($this->config[GraphQLObjectRootTypeConfig::TEMPLATE_TYPE]) ?
            $this->config[GraphQLObjectRootTypeConfig::TEMPLATE_TYPE] : null;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateType($templateTypeName)
    {
        $this->config[GraphQLObjectRootTypeConfig::TEMPLATE_TYPE] = $templateTypeName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRootDataProvider()
    {
        return isset($this->config[GraphQLObjectRootTypeConfig::ROOT_DATA_PROVIDER]) ?
            $this->config[GraphQLObjectRootTypeConfig::ROOT_DATA_PROVIDER] : null;
    }

    /**
     * @inheritDoc
     */
    public function setRootDataProvider(BuilderiusGraphQLRootDataProviderInterface $rootDataProvider)
    {
        $this->config[GraphQLObjectRootTypeConfig::ROOT_DATA_PROVIDER] = $rootDataProvider;

        return $this;
    }
}