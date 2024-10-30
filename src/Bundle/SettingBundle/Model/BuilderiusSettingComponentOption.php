<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSettingComponentOption extends ParameterBag
{
    const NAME_FIELD = 'name';
    const TYPE_FIELD = 'type';
    const REQUIRED_FIELD = 'required';
    const DEFAULT_FIELD = 'default';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->get(self::REQUIRED_FIELD, false);
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->get(self::DEFAULT_FIELD);
    }
}
