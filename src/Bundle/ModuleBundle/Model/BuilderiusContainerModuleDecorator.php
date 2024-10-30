<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;

class BuilderiusContainerModuleDecorator extends BuilderiusModuleDecorator implements BuilderiusContainerModuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(BuilderiusModuleInterface $module)
    {
        if (!$module instanceof BuilderiusContainerModuleInterface) {
            throw new \Exception(
                sprintf(
                    '%s can decorate only classes which implements %s',
                    self::class,
                    BuilderiusContainerModuleInterface::class
                )
            );
        }
        if ($module instanceof AssetAwareInterface) {
            throw new \Exception(
                sprintf(
                    '%s can\'t decorate classes which implements %s',
                    self::class,
                    AssetAwareInterface::class
                )
            );
        }
        $this->module = $module;
    }

    /**
     * @inheritDoc
     */
    public function setContainerFor(array $modules)
    {
        $this->module->setContainerFor($modules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addContainerFor($module)
    {
        $this->module->addContainerFor($module);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainerFor()
    {
        return $this->module->getContainerFor();
    }

    /**
     * @inheritDoc
     */
    public function setNotContainerFor(array $modules)
    {
        $this->module->setNotContainerFor($modules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addNotContainerFor($module)
    {
        $this->module->addNotContainerFor($module);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNotContainerFor()
    {
        return $this->module->getNotContainerFor();
    }
}