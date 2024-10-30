<?php

namespace Builderius\Bundle\ModuleBundle\Provider;

use Builderius\Bundle\ModuleBundle\Checker\BuilderiusModuleCheckerInterface;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusModuleSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface;

class BuilderiusRegularModulesProvider implements BuilderiusModulesProviderInterface
{
    /**
     * @var array
     */
    private $modulesByTypesAndTechs = [];

    /**
     * @var BuilderiusModuleCheckerInterface
     */
    private $checker;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var ConditionInterface
     */
    private $isBuilderModeCondition;

    /**
     * @param BuilderiusModuleCheckerInterface $checker
     * @param BuilderiusSettingsRegistryInterface $modulesSettingsRegistry
     * @param BuilderiusRuntimeObjectCache $cache
     * @param ConditionInterface $isBuilderModeCondition
     */
    public function __construct(
        BuilderiusModuleCheckerInterface $checker,
        BuilderiusSettingsRegistryInterface $modulesSettingsRegistry,
        BuilderiusRuntimeObjectCache $cache,
        ConditionInterface $isBuilderModeCondition
    ) {
        $this->checker = $checker;
        $this->settingsRegistry = $modulesSettingsRegistry;
        $this->cache = $cache;
        $this->isBuilderModeCondition = $isBuilderModeCondition;
    }

    /**
     * @param BuilderiusModuleInterface $module
     */
    public function addModule(BuilderiusModuleInterface $module)
    {
        if ($this->isBuilderModeCondition->evaluate()) {
            if ($this->checker->check($module)) {
                $this->preprocessModule($module);
            }
        } else {
            $this->preprocessModule($module);
        }
    }

    /**
     * @param BuilderiusModuleInterface $module
     */
    private function preprocessModule(BuilderiusModuleInterface $module)
    {
        foreach ($module->getTemplateTypes() as $type) {
            if (!isset($this->modulesByTypesAndTechs[$type])) {
                $this->modulesByTypesAndTechs[$type] = [];
            }
            foreach ($module->getTechnologies() as $technology) {
                if (!isset($this->modulesByTypesAndTechs[$type][$technology])) {
                    $this->modulesByTypesAndTechs[$type][$technology] = [];
                }
                if (!in_array($module, $this->modulesByTypesAndTechs[$type][$technology])) {
                    $this->modulesByTypesAndTechs[$type][$technology][$module->getName()] = $module;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getModules($templateType, $technology, $withSettings = true, $configVersions = [])
    {
        $cacheKey = sprintf('builderius_%s_%s_regular_modules_%s_%s', $templateType, $technology, $withSettings, json_encode($configVersions));
        $modules = $this->cache->get($cacheKey);
        if (false === $modules) {
            if ($templateType === 'all') {
                $templateType = 'template';
            }
            if ($technology === 'all') {
                $technology = 'html';
            }
            if (!isset($this->modulesByTypesAndTechs[$templateType][$technology])) {
                return [];
            }
            if (true === $withSettings) {
                $settings = $this->settingsRegistry->getSettings($templateType, $technology);
                /** @var BuilderiusModuleInterface $module */
                foreach ($this->modulesByTypesAndTechs[$templateType][$technology] as $name => $module) {
                    /** @var BuilderiusSettingInterface|BuilderiusModuleSettingInterface $setting */
                    foreach ($settings as $setting) {
                        if ($setting instanceof BuilderiusModuleSettingInterface) {
                            if ($setting instanceof BuilderiusSettingCssAwareInterface && $module->isAllCssSettingsExcluded()) {
                                continue;
                            } elseif (in_array($setting->getName(), $module->getExcludedSettings())) {
                                continue;
                            } else {
                                if (
                                    (($setting->isAppliedToAllTemplateTypes() && !in_array($templateType, $setting->getExcludedFromTemplateTypes())) || in_array($templateType, $setting->getAppliedToTemplateTypes())) &&
                                    (($setting->isAppliedToAllTechnologies() && !in_array($technology, $setting->getExcludedFromTechnologies())) || in_array($technology, $setting->getAppliedToTechnologies())) &&
                                    (($setting->isAppliedToAllModules() && !in_array($name, $setting->getExcludedFromModules())) || in_array($name, $setting->getAppliedToModules()))
                                ) {
                                    $module->addSetting($setting);
                                }
                            }
                        }
                    }
                }
            }

            $modules = $this->modulesByTypesAndTechs[$templateType][$technology];
            $filteredModules = [];
            foreach ($modules as $name => $module) {
                $aConfVersion = $module->getConfigVersions();
                if ((empty($aConfVersion) && empty($configVersions)) || (empty($aConfVersion) && !empty($configVersions))) {
                    $filteredModules[$name] = $module;
                } else {
                    foreach ($configVersions as $plugin => $pluginVersion) {
                        if (isset($aConfVersion[$plugin]) && version_compare($pluginVersion, $aConfVersion[$plugin]) < 1) {
                            $filteredModules[$name] = $module;
                        }
                    }
                }
            }
            $modules = $filteredModules;
            $this->cache->set($cacheKey, $modules);
        }

        return $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function getModule($name, $templateType, $technology, $withSettings = true, $configVersions = [])
    {
        if ($this->hasModule($name, $templateType, $technology, $withSettings, $configVersions)) {
            return $this->getModules($templateType, $technology, $withSettings, $configVersions)[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasModule($name, $templateType, $technology, $withSettings = true, $configVersions = [])
    {
        return array_key_exists($name, $this->getModules($templateType, $technology, $withSettings, $configVersions));
    }
}
