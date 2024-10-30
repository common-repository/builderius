<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingValueInterface
{
    /**
     * @param array $value
     * @return $this
     */
    public function setValue(array $value);
    
    /**
     * @return array
     */
    public function getValue();

    /**
     * @param boolean $default
     * @return $this
     */
    public function setDefault($default);

    /**
     * @return boolean
     */
    public function isDefault();
}
