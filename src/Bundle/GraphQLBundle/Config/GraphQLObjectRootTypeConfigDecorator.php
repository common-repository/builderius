<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\Bundle\GraphQLBundle\Provider\RootData\BuilderiusGraphQLRootDataProviderInterface;

class GraphQLObjectRootTypeConfigDecorator implements GraphQLObjectRootTypeConfigInterface
{
    /**
     * @var GraphQLObjectRootTypeConfigInterface
     */
    private $decoratedRootTypeConfig;

    /**
     * @param GraphQLObjectRootTypeConfigInterface $decoratedRootTypeConfig
     */
    public function __construct(GraphQLObjectRootTypeConfigInterface $decoratedRootTypeConfig)
    {
        $this->decoratedRootTypeConfig = $decoratedRootTypeConfig;
    }

    /**
     * @inheritDoc
     */
    public function getInterfaces()
    {
        return $this->decoratedRootTypeConfig->getInterfaces();
    }

    /**
     * @inheritDoc
     */
    public function setInterfaces(array $interfaces)
    {
        $this->decoratedRootTypeConfig->setInterfaces($interfaces);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addInterface(GraphQLInterfaceTypeConfigInterface $interface)
    {
        $this->decoratedRootTypeConfig->addInterface($interface);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return $this->decoratedRootTypeConfig->getFields();
    }

    /**
     * @inheritDoc
     */
    public function setFields(array $fields)
    {
        $this->decoratedRootTypeConfig->setFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(GraphQLFieldConfigInterface $field)
    {
        $this->decoratedRootTypeConfig->addField($field);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->decoratedRootTypeConfig->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->decoratedRootTypeConfig->setName($name);

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->decoratedRootTypeConfig->getDescription();
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->decoratedRootTypeConfig->setDescription($description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllTemplateTypes()
    {
        return $this->decoratedRootTypeConfig->isAppliedToAllTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTemplateTypes($appliedToAll = false)
    {
        $this->decoratedRootTypeConfig->setAppliedToAllTemplateTypes($appliedToAll);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateType()
    {
        return $this->decoratedRootTypeConfig->getTemplateType();
    }

    /**
     * @inheritDoc
     */
    public function setTemplateType($templateTypeName)
    {
        $this->decoratedRootTypeConfig->setTemplateType($templateTypeName);
    }

    /**
     * @inheritDoc
     */
    public function getRootDataProvider()
    {
        return $this->decoratedRootTypeConfig->getRootDataProvider();
    }

    /**
     * @inheritDoc
     */
    public function setRootDataProvider(BuilderiusGraphQLRootDataProviderInterface $rootDataProvider)
    {
        $this->decoratedRootTypeConfig->setRootDataProvider($rootDataProvider);

        return $this;
    }
}