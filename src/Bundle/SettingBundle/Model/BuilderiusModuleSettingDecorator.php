<?php

namespace Builderius\Bundle\SettingBundle\Model;

class BuilderiusModuleSettingDecorator extends AbstractBuilderiusSettingDecorator
    implements BuilderiusModuleSettingInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(BuilderiusSettingInterface $setting)
    {
        if (!$setting instanceof BuilderiusModuleSettingInterface) {
            throw new \Exception(
                sprintf(
                    '%s can decorate only classes which implements %s',
                    self::class,
                    BuilderiusModuleSettingInterface::class
                )
            );
        }
        if ($setting instanceof BuilderiusSettingCssAwareInterface) {
            throw new \Exception(
                sprintf(
                    '%s can\'t decorate classes which implements %s',
                    self::class,
                    BuilderiusSettingCssAwareInterface::class
                )
            );
        }

        parent::__construct($setting);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToModules(array $modules)
    {
        $this->setting->setAppliedToModules($modules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAppliedToModule($module)
    {
        $this->setting->addAppliedToModule($module);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAppliedToModules()
    {
        return $this->setting->getAppliedToModules();
    }

    /**
     * @inheritDoc
     */
    public function setExcludedFromModules(array $modules)
    {
        $this->setting->setExcludedFromModules($modules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedFromModule($module)
    {
        $this->setting->addExcludedFromModule($module);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedFromModules()
    {
        return $this->setting->getExcludedFromModules();
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllModules()
    {
        return $this->setting->isAppliedToAllModules();
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllModules($appliedToAllModules)
    {
        $this->setting->setAppliedToAllModules($appliedToAllModules);

        return $this;
    }
}