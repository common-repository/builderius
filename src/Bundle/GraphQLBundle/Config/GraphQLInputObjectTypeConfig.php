<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

class GraphQLInputObjectTypeConfig extends AbstractGraphQLTypeConfig implements GraphQLInputObjectTypeConfigInterface
{
    const FIELDS_FIELD = 'fields';
    const INTERFACES_FIELD = 'interfaces';

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return $this->get(self::FIELDS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(GraphQLFieldConfigInterface $field)
    {
        $fields = $this->getFields();
        $fields[$field->getName()] = $field;
        $this->set(self::FIELDS_FIELD, $fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInterfaces()
    {
        return $this->get(self::INTERFACES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setInterfaces(array $interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->addInterface($interface);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addInterface(GraphQLInterfaceTypeConfigInterface $interface)
    {
        $interfaces = $this->getInterfaces();
        $interfaces[$interface->getName()] = $interface;
        $this->set(self::INTERFACES_FIELD, $interfaces);

        return $this;
    }
}