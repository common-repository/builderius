<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

class GraphQLEnumTypeConfig extends AbstractGraphQLTypeConfig implements GraphQLEnumTypeConfigInterface
{
    const VALUES_FIELD = 'values';

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return $this->get(self::VALUES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setValues(array $values)
    {
        foreach ($values as $value) {
            $this->addValue($value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addValue(GraphQLEnumValueConfigInterface $value)
    {
        $values = $this->getValues();
        $values[$value->getName()] = $value;
        $this->set(self::VALUES_FIELD, $values);

        return $this;
    }
}