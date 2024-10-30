<?php

namespace Builderius\Bundle\ModuleBundle\Model;

trait BuilderiusContainerModuleTrait
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @inheritDoc
     */
    public function getContainerFor()
    {
        return \array_key_exists(
            BuilderiusContainerModuleInterface::CONTAINER_FOR_FIELD,
            $this->parameters
        ) ? $this->parameters[BuilderiusContainerModuleInterface::CONTAINER_FOR_FIELD] : [];
    }

    /**
     * @inheritDoc
     */
    public function addContainerFor($module)
    {
        $modules = $this->getContainerFor();
        if (!in_array($module, $modules)) {
            $modules[] = $module;
            $this->setContainerFor($modules);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainerFor(array $modules)
    {
        $this->parameters[BuilderiusContainerModuleInterface::CONTAINER_FOR_FIELD] = $modules;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNotContainerFor()
    {
        return \array_key_exists(
            BuilderiusContainerModuleInterface::NOT_CONTAINER_FOR_FIELD,
            $this->parameters
        ) ? $this->parameters[BuilderiusContainerModuleInterface::NOT_CONTAINER_FOR_FIELD] : [];
    }

    /**
     * @inheritDoc
     */
    public function addNotContainerFor($module)
    {
        $modules = $this->getNotContainerFor();
        if (!in_array($module, $modules)) {
            $modules[] = $module;
            $this->setNotContainerFor($modules);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setNotContainerFor(array $modules)
    {
        $this->parameters[BuilderiusContainerModuleInterface::NOT_CONTAINER_FOR_FIELD] = $modules;

        return $this;
    }
}