<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

interface GraphQLInputObjectTypeConfigInterface extends GraphQLTypeConfigInterface
{
    /**
     * @return GraphQLFieldConfigInterface[]
     */
    public function getFields();

    /**
     * @param GraphQLFieldConfigInterface[] $fields
     * @return $this
     */
    public function setFields(array $fields);

    /**
     * @param GraphQLFieldConfigInterface $field
     * @return $this
     */
    public function addField(GraphQLFieldConfigInterface $field);

    /**
     * @return GraphQLInterfaceTypeConfigInterface[]
     */
    public function getInterfaces();

    /**
     * @param GraphQLInterfaceTypeConfigInterface[] $interfaces
     * @return $this
     */
    public function setInterfaces(array $interfaces);

    /**
     * @param GraphQLInterfaceTypeConfigInterface $interface
     * @return $this
     */
    public function addInterface(GraphQLInterfaceTypeConfigInterface $interface);
}