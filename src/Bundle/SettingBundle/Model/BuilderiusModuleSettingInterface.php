<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusModuleSettingInterface
{
    /**
     * @param array $modules
     * @return $this
     */
    public function setAppliedToModules(array $modules);

    /**
     * @param string $module
     * @return $this
     */
    public function addAppliedToModule($module);

    /**
     * @return array
     */
    public function getAppliedToModules();

    /**
     * @param array $modules
     * @return $this
     */
    public function setExcludedFromModules(array $modules);

    /**
     * @param string $module
     * @return $this
     */
    public function addExcludedFromModule($module);

    /**
     * @return array
     */
    public function getExcludedFromModules();

    /**
     * @return bool
     */
    public function isAppliedToAllModules();

    /**
     * @param bool $appliedToAllModules
     * @return $this
     */
    public function setAppliedToAllModules($appliedToAllModules);
}
