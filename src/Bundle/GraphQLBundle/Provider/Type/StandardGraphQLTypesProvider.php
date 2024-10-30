<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\Type;

use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\Type;

class StandardGraphQLTypesProvider implements GraphQLTypesProviderInterface
{
    /**
     * @var Type[]
     */
    private $types = [];

    /**
     * @param Type $type
     * @return $this
     */
    public function addType(Type $type)
    {
        if ($type->name) {
            $this->types[$type->name] = $type;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @inheritDoc
     */
    public function getType($name)
    {
        $nonNull = substr($name, -1) === '!' ? true : false;
        if ($nonNull) {
            $name = substr($name, 0, -1);
        }
        $listOf = (substr($name, 0, 1) === '[' && substr($name, -1) === ']') ? true : false;
        if ($listOf) {
            $name = substr(substr($name, 0, -1), 1);
        }
        $type = $this->hasType($name) ? $this->types[$name] : null;

        return $nonNull ? new NonNull($type) : ($listOf ? new ListOfType($type) : $type);
    }

    /**
     * @inheritDoc
     */
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }
}