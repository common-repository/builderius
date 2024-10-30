<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingComponentInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return BuilderiusSettingComponentOption[]
     */
    public function getAcceptableOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setAcceptableOptions(array $options);

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function addAcceptableOption($name, array $arguments);
}
