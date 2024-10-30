<?php

namespace Builderius\Bundle\SettingBundle\Model;

class BuilderiusSettingDecorator extends AbstractBuilderiusSettingDecorator
    implements BuilderiusSettingInterface
{
    /**
     * @inheritDoc
     */
    public function __construct(BuilderiusSettingInterface $setting)
    {
        if ($setting instanceof BuilderiusSettingCssAwareInterface) {
            throw new \Exception(
                sprintf(
                    '%s can\'t decorate classes which implements %s',
                    self::class,
                    BuilderiusSettingCssAwareInterface::class
                )
            );
        }
        if ($setting instanceof BuilderiusModuleSettingInterface) {
            throw new \Exception(
                sprintf(
                    '%s can\'t decorate classes which implements %s',
                    self::class,
                    BuilderiusModuleSettingInterface::class
                )
            );
        }

        parent::__construct($setting);
    }

}