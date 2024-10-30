<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSettingValue extends ParameterBag implements BuilderiusSettingValueInterface
{
    const VALUE_FIELD = 'value';
    const DEFAULT_FIELD = 'default';

    /**
     * @inheritDoc
     */
    public function setValue(array $value)
    {
        $this->set(self::VALUE_FIELD, $value);
        
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
    public function setDefault($default)
    {
        $this->set(self::DEFAULT_FIELD, $default);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDefault()
    {
        return (bool)$this->get(self::DEFAULT_FIELD, false);
    }
}
