<?php

namespace Builderius\Bundle\SettingBundle\Registry;

use Builderius\Bundle\SettingBundle\Generator\GlobalSetting\BuilderiusGlobalSettingValueGeneratorInterface;

class BuilderiusGlobalSettingValueGeneratorsRegistry implements BuilderiusGlobalSettingValueGeneratorsRegistryInterface
{
    /**
     * @var BuilderiusGlobalSettingValueGeneratorInterface[]
     */
    private $generators = [];

    public function addGenerator(BuilderiusGlobalSettingValueGeneratorInterface $generator)
    {
        $this->generators[$generator->getSettingName()] = $generator;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGenerators()
    {
        return $this->generators;
    }

    /**
     * @inheritDoc
     */
    public function getGenerator($settingName)
    {
        if ($this->hasGenerator($settingName)) {
            return $this->getGenerators()[$settingName];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasGenerator($settingName)
    {
        return array_key_exists($settingName, $this->getGenerators());
    }
}