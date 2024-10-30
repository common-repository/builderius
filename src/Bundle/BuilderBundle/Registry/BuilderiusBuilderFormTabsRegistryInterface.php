<?php

namespace Builderius\Bundle\BuilderBundle\Registry;

use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderFormTabInterface;

interface BuilderiusBuilderFormTabsRegistryInterface
{
    /**
     * @return BuilderiusBuilderFormTabInterface[]
     */
    public function getTabs();
    
    /**
     * @param string $name
     * @return BuilderiusBuilderFormTabInterface
     */
    public function getTab($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasTab($name);
}
