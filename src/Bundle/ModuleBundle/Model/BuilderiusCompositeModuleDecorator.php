<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;

class BuilderiusCompositeModuleDecorator extends BuilderiusModuleDecorator implements BuilderiusCompositeModuleInterface
{
    /**
     * @var BuilderiusCompositeModuleInterface
     */
    protected $module;

    /**
     * @param BuilderiusCompositeModuleInterface $module
     */
    public function __construct(BuilderiusCompositeModuleInterface $module)
    {
        if ($module instanceof BuilderiusContainerModuleInterface) {
            throw new \Exception(
                sprintf(
                    '%s can\'t decorate classes which implements %s',
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
    public function getConfig()
    {
        return $this->module->getConfig();
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        $this->module->setConfig($config);

        return $this;
    }
}