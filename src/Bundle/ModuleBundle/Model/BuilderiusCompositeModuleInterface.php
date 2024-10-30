<?php

namespace Builderius\Bundle\ModuleBundle\Model;

interface BuilderiusCompositeModuleInterface
{
    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);

}
