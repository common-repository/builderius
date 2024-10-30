<?php

namespace Builderius\Bundle\ModuleBundle\Checker;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;

interface BuilderiusModuleCheckerInterface
{
    /**
     * @param BuilderiusModuleInterface $module
     * @return boolean
     */
    public function check(BuilderiusModuleInterface $module);
}
