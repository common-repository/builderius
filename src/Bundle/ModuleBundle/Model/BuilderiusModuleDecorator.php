<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;

class BuilderiusModuleDecorator implements BuilderiusModuleInterface
{
    /**
     * @var BuilderiusModuleInterface
     */
    protected $module;

    /**
     * @param BuilderiusModuleInterface $module
     */
    public function __construct(BuilderiusModuleInterface $module)
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
    public function getName()
    {
        return $this->module->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->module->setName($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->module->getIcon();
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        $this->module->setIcon($icon);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->module->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->module->setLabel($label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHtmlTemplate()
    {
        return $this->module->getHtmlTemplate();
    }

    /**
     * @inheritDoc
     */
    public function setHtmlTemplate($template)
    {
        $this->module->setHtmlTemplate($template);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateTypes()
    {
        return $this->module->getTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setTemplateTypes(array $templateTypes)
    {
        $this->module->setTemplateTypes($templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTemplateType($templateType)
    {
        $this->module->addTemplateType($templateType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->module->getTechnologies();
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies($technologies)
    {
        $this->module->setTechnologies($technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology($technology)
    {
        $this->module->addTechnology($technology);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTags()
    {
        return $this->module->getTags();
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags)
    {
        $this->module->setTags($tags);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTag($tag)
    {
        $this->module->addTag($tag);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->module->getCategory();
    }

    /**
     * @inheritDoc
     */
    public function setCategory($category)
    {
        $this->module->setCategory($category);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettings()
    {
        return $this->module->getSettings();
    }

    /**
     * @inheritDoc
     */
    public function getSetting($name)
    {
        return $this->module->getSetting($name);
    }

    /**
     * @inheritDoc
     */
    public function hasSetting($name)
    {
        return $this->module->hasSetting($name);
    }

    /**
     * @inheritDoc
     */
    public function addSetting(BuilderiusSettingInterface $setting)
    {
        $this->module->addSetting($setting);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setExcludedSettings(array $settings = [])
    {
        $this->module->setExcludedSettings($settings);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedSettings()
    {
        return $this->module->getExcludedSettings();
    }

    /**
     * @inheritDoc
     */
    public function addExcludedSettings(array $settings = [])
    {
        $this->module->addExcludedSettings($settings);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function excludeAllCssSettings($excludeAllCssSettings = false)
    {
        $this->module->excludeAllCssSettings($excludeAllCssSettings);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAllCssSettingsExcluded()
    {
        return $this->module->isAllCssSettingsExcluded();
    }

    /**
     * @inheritDoc
     */
    public function isPublic()
    {
        return $this->module->isPublic();
    }

    /**
     * @inheritDoc
     */
    public function setPublic($public)
    {
        $this->module->setPublic($public);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConfigVersions()
    {
        return $this->module->getConfigVersions();
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->module->getSortOrder();
    }
}