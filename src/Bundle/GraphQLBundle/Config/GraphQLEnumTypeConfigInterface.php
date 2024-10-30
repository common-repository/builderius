<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

interface GraphQLEnumTypeConfigInterface extends GraphQLTypeConfigInterface
{
    /**
     * @return GraphQLEnumValueConfigInterface[]
     */
    public function getValues();

    /**
     * @param GraphQLEnumValueConfigInterface[] $values
     * @return $this
     */
    public function setValues(array $values);

    /**
     * @param GraphQLEnumValueConfigInterface $value
     * @return $this
     */
    public function addValue(GraphQLEnumValueConfigInterface $value);
}