<?php

namespace Builderius\Bundle\ModuleBundle\Provider;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusCompositeModuleInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;

class BuilderiusCompositeRegularModulesProvider implements BuilderiusModulesProviderInterface
{
    /**
     * @var array
     */
    private $modulesByTypesAndTechs = [];

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->cache = $cache;
    }

    /**
     * @param BuilderiusCompositeModuleInterface $module
     */
    public function addModule(BuilderiusCompositeModuleInterface $module)
    {
        $this->preprocessModule($module);
    }

    /**
     * @param BuilderiusCompositeModuleInterface $module
     */
    private function preprocessModule(BuilderiusCompositeModuleInterface $module)
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
        $cacheKey = sprintf('builderius_%s_%s_regular_composite_modules_%s_%s', $templateType, $technology, $withSettings, json_encode($configVersions));
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

            $modules = $this->modulesByTypesAndTechs[$templateType][$technology];
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
