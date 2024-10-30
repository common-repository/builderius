<?php


namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class GraphQLEnumValueConfig extends ParameterBag implements GraphQLEnumValueConfigInterface
{
    const NAME_FIELD = 'name';
    const DESCRIPTION_FIELD = 'description';
    const VALUE_FIELD = 'value';

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
    public function getValue()
    {
        return $this->get(self::VALUE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->set(self::VALUE_FIELD, $value);

        return $this;
    }
}