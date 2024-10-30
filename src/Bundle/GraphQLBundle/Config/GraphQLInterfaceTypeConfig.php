<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLTypeResolverInterface;

class GraphQLInterfaceTypeConfig extends AbstractGraphQLTypeConfig implements GraphQLInterfaceTypeConfigInterface
{
    const FIELDS_FIELD = 'fields';
    const TYPE_RESOLVER_FIELD = 'resolveType';

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
    public function getTypeResolver()
    {
        return $this->get(self::TYPE_RESOLVER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setTypeResolver(GraphQLTypeResolverInterface $resolver)
    {
        $this->set(self::TYPE_RESOLVER_FIELD, $resolver);
    }
}