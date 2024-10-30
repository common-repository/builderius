<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\Bundle\GraphQLBundle\Provider\RootData\BuilderiusGraphQLRootDataProviderInterface;

class GraphQLObjectRootTypeConfig extends GraphQLObjectTypeConfig implements GraphQLObjectRootTypeConfigInterface
{
    const APPLIED_TO_ALL_TEMPLATE_TYPES = 'appliedToAllTemplateTypes';
    const TEMPLATE_TYPE = 'templateType';
    const ROOT_DATA_PROVIDER = 'rootDataProvider';

    /**
     * @inheritDoc
     */
    public function isAppliedToAllTemplateTypes()
    {
        return $this->get(self::APPLIED_TO_ALL_TEMPLATE_TYPES, false);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTemplateTypes($appliedToAll = false)
    {
        $this->set(self::APPLIED_TO_ALL_TEMPLATE_TYPES, $appliedToAll);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateType()
    {
        return $this->get(self::TEMPLATE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setTemplateType($templateTypeName)
    {
        $this->set(self::TEMPLATE_TYPE, $templateTypeName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRootDataProvider()
    {
        return $this->get(self::ROOT_DATA_PROVIDER);
    }

    /**
     * @inheritDoc
     */
    public function setRootDataProvider(BuilderiusGraphQLRootDataProviderInterface $rootDataProvider)
    {
        $this->set(self::ROOT_DATA_PROVIDER, $rootDataProvider);

        return $this;
    }
}