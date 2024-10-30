<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

class GraphQLObjectTypeConfigDecorator implements GraphQLObjectTypeConfigInterface
{
    /**
     * @var GraphQLObjectTypeConfigInterface
     */
    private $decoratedTypeConfig;

    /**
     * @param GraphQLObjectTypeConfigInterface $decoratedTypeConfig
     */
    public function __construct(GraphQLObjectTypeConfigInterface $decoratedTypeConfig)
    {
        $this->decoratedTypeConfig = $decoratedTypeConfig;
    }

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return $this->decoratedTypeConfig->getFields();
    }

    /**
     * @inheritDoc
     */
    public function setFields(array $fields)
    {
        $this->decoratedTypeConfig->setFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(GraphQLFieldConfigInterface $field)
    {
        $this->decoratedTypeConfig->addField($field);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInterfaces()
    {
        return $this->decoratedTypeConfig->getInterfaces();
    }

    /**
     * @inheritDoc
     */
    public function setInterfaces(array $interfaces)
    {
        $this->decoratedTypeConfig->setInterfaces($interfaces);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addInterface(GraphQLInterfaceTypeConfigInterface $interface)
    {
        $this->decoratedTypeConfig->addInterface($interface);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->decoratedTypeConfig->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->decoratedTypeConfig->setName($name);

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->decoratedTypeConfig->getDescription();
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->decoratedTypeConfig->setDescription($description);

        return $this;
    }
}