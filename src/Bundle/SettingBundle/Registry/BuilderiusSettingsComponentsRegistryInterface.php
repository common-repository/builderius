<?php

namespace Builderius\Bundle\SettingBundle\Registry;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingComponentInterface;

interface BuilderiusSettingsComponentsRegistryInterface
{
    /**
     * @return BuilderiusSettingComponentInterface[]
     */
    public function getComponents();
    
    /**
     * @param string $name
     * @return BuilderiusSettingComponentInterface
     */
    public function getComponent($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasComponent($name);
}
