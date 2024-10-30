<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusModule extends ParameterBag implements BuilderiusModuleInterface
{
    const NAME_FIELD = 'name';
    const ICON_FIELD = 'icon';
    const PUBLIC_FIELD = 'public';
    const LABEL_FIELD = 'label';
    const HTML_TEMPLATE_FIELD = 'htmlTemplate';
    const TAGS_FIELD = 'tags';
    const TEMPLATE_TYPES_FIELD = 'templateTypes';
    const TECHNOLOGIES_FIELD = 'technologies';
    const CATEGORY_FIELD = 'category';
    const SETTINGS_FIELD = 'settings';
    const EXCLUDE_SETTINGS = 'exclude_settings';
    const EXCLUDE_ALL_CSS_SETTINGS = 'exclude_all_css_settings';
    const PRESETS_FIELD = 'presets';
    const CONFIG_VERSIONS_FIELD = 'config_versions';
    const SORT_ORDER_FIELD = 'sort_order';

    const ADD_SETTING_TAG = 'builderius_module_add_setting';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, sanitize_text_field($name));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->get(self::ICON_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        $this->set(self::ICON_FIELD, $icon);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __($this->get(self::LABEL_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, sanitize_text_field($label));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTemplate()
    {
        return $this->get(self::HTML_TEMPLATE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setHtmlTemplate($template)
    {
        $this->set(self::HTML_TEMPLATE_FIELD, $template);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateTypes()
    {
        return $this->get(self::TEMPLATE_TYPES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTemplateTypes(array $templateTypes)
    {
        $this->set(self::TEMPLATE_TYPES_FIELD, $templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTemplateType($templateType)
    {
        $templateTypes = $this->getTemplateTypes();
        if (!in_array($templateType, $templateTypes)) {
            $templateTypes[] = $templateType;
            $this->set(self::TEMPLATE_TYPES_FIELD, $templateTypes);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->get(self::TECHNOLOGIES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies(array $technologies)
    {
        $this->set(self::TECHNOLOGIES_FIELD, $technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology($technology)
    {
        $technologies = $this->getTechnologies();
        if (!in_array($technology, $technologies)) {
            $technologies[] = $technology;
            $this->set(self::TECHNOLOGIES_FIELD, $technologies);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->get(self::TAGS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags)
    {
        $this->set(self::TAGS_FIELD, $tags);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTag($tag)
    {
        $tags = $this->getTags();
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->set(self::TAGS_FIELD, $tags);
        }
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setCategory($category)
    {
        $this->set(self::CATEGORY_FIELD, $category);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        $settings = $this->get(self::SETTINGS_FIELD, []);
        $isAllCssSettingsExcluded = $this->isAllCssSettingsExcluded();
        $excludedSettings = $this->getExcludedSettings();
        foreach ($settings as $name => $setting) {
            if ($isAllCssSettingsExcluded && $setting instanceOf BuilderiusSettingCssAwareInterface) {
                unset($settings[$name]);
            }
            if (in_array($name, $excludedSettings)) {
                unset($settings[$name]);
            }
        }

        return $settings;
    }

    /**
     * @inheritDoc
     */
    public function getSetting($name)
    {
        if ($this->hasSetting($name)) {
            return $this->getSettings()[$name];
        }
        
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasSetting($name)
    {
        return isset($this->getSettings()[$name]);
    }

    /**
     * @inheritDoc
     */
    public function addSetting(BuilderiusSettingInterface $setting)
    {
        $setting = apply_filters(self::ADD_SETTING_TAG, $setting, $this);
        $settings = $this->get(self::SETTINGS_FIELD, []);
        $settings[$setting->getName()] = $setting;
        $this->set(self::SETTINGS_FIELD, $settings);
    }

    /**
     * @inheritDoc
     */
    public function setExcludedSettings(array $settings = [])
    {
        $this->set(self::EXCLUDE_SETTINGS, $settings);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedSettings()
    {
        return $this->get(self::EXCLUDE_SETTINGS, []);
    }

    /**
     * @inheritDoc
     */
    public function addExcludedSettings(array $settings = [])
    {
        $exSettings = $this->get(self::EXCLUDE_SETTINGS, []);
        foreach ($settings as $setting) {
            if (!in_array($setting, $exSettings)) {
                $exSettings[] = $setting;
            }
        }
        $this->set(self::EXCLUDE_SETTINGS, $exSettings);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function excludeAllCssSettings($excludeAllCssSettings = false)
    {
        $this->set(self::EXCLUDE_ALL_CSS_SETTINGS, $excludeAllCssSettings);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAllCssSettingsExcluded()
    {
        return $this->get(self::EXCLUDE_ALL_CSS_SETTINGS, false);
    }

    /**
     * @inheritDoc
     */
    public function isPublic()
    {
        return $this->get(self::PUBLIC_FIELD, true);
    }

    /**
     * @inheritDoc
     */
    public function setPublic($public)
    {
        $this->set(self::PUBLIC_FIELD, $public);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConfigVersions()
    {
        return $this->get(self::CONFIG_VERSIONS_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 10);
    }
}
