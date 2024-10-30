<?php

namespace Builderius\Bundle\ModuleBundle\Model;

interface BuilderiusContainerModuleInterface
{
    const CONTAINER_FIELD = 'container';
    const CONTAINER_FOR_FIELD = 'containerFor';
    const NOT_CONTAINER_FOR_FIELD = 'notContainerFor';

    /**
     * @param array $modules
     * @return $this
     */
    public function setContainerFor(array $modules);

    /**
     * @param string $module
     * @return $this
     */
    public function addContainerFor($module);

    /**
     * @return array
     */
    public function getContainerFor();

    /**
     * @param array $modules
     * @return $this
     */
    public function setNotContainerFor(array $modules);

    /**
     * @param string $module
     * @return $this
     */
    public function addNotContainerFor($module);

    /**
     * @return array
     */
    public function getNotContainerFor();
}