<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLTypeResolverInterface;

interface GraphQLInterfaceTypeConfigInterface extends GraphQLTypeConfigInterface
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
     * @return GraphQLTypeResolverInterface
     */
    public function getTypeResolver();

    /**
     * @param GraphQLTypeResolverInterface $resolver
     * @return $this
     */
    public function setTypeResolver(GraphQLTypeResolverInterface $resolver);
}