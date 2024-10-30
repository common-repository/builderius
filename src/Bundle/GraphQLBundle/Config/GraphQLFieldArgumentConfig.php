<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class GraphQLFieldArgumentConfig extends ParameterBag implements GraphQLFieldArgumentConfigInterface
{
    const NAME_FIELD = 'name';
    const TYPE_FIELD = 'type';
    const DESCRIPTION_FIELD = 'description';
    const DEFAULT_VALUE_FIELD = 'defaultValue';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->get(self::DESCRIPTION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->set(self::DESCRIPTION_FIELD, $description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return $this->get(self::DEFAULT_VALUE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultValue($defaultValue)
    {
        $this->set(self::DEFAULT_VALUE_FIELD, $defaultValue);

        return $this;
    }
}