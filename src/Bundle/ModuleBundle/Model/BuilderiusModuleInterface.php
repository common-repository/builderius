<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

interface BuilderiusModuleInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);
    
    /**
     * @return string
     */
    public function getHtmlTemplate();

    /**
     * @param string $template
     * @return $this
     */
    public function setHtmlTemplate($template);

    /**
     * @return array
     */
    public function getTemplateTypes();

    /**
     * @param array $templateTypes
     * @return $this
     */
    public function setTemplateTypes(array $templateTypes);

    /**
     * @param string $templateType
     * @return $this
     */
    public function addTemplateType($templateType);

    /**
     * @return array
     */
    public function getTechnologies();

    /**
     * @param array $technologies
     * @return $this
     */
    public function setTechnologies(array $technologies);

    /**
     * @param string $technology
     * @return $this
     */
    public function addTechnology($technology);

    /**
     * @return array
     */
    public function getTags();

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags);

    /**
     * @param string $tag
     * @return $this
     */
    public function addTag($tag);

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category);

    /**
     * @return BuilderiusSettingInterface[]
     */
    public function getSettings();
    
    /**
     * @param string $name
     * @return BuilderiusSettingInterface|null
     */
    public function getSetting($name);

    /**
     * @param string $name
     * @return boolean
     */
    public function hasSetting($name);

    /**
     * @param BuilderiusSettingInterface $setting
     * @return $this
     */
    public function addSetting(BuilderiusSettingInterface $setting);

    /**
     * @param array $settings
     * @return $this
     */
    public function setExcludedSettings(array $settings = []);

    /**
     * @return array
     */
    public function getExcludedSettings();

    /**
     * @param string $setting
     * @return $this
     */
    public function addExcludedSettings(array $settings = []);

    /**
     * @param bool $excludeAllCssSettings
     * @return $this
     */
    public function excludeAllCssSettings($excludeAllCssSettings = false);

    /**
     * @return bool
     */
    public function isAllCssSettingsExcluded();

    /**
     * @return bool
     */
    public function isPublic();

    /**
     * @param bool $public
     * @return $this
     */
    public function setPublic($public);

    /**
     * @return array
     */
    public function getConfigVersions();

    /**
     * @return int
     */
    public function getSortOrder();
}
