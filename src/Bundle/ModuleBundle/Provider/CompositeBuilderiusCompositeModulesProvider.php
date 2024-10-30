<?php

namespace Builderius\Bundle\ModuleBundle\Provider;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;

class CompositeBuilderiusCompositeModulesProvider implements BuilderiusModulesProviderInterface
{
    /**
     * @var BuilderiusModulesProviderInterface[]
     */
    private $providers = [];

    /**
     * @var BuilderiusModuleInterface[]
     */
    private $modules = [];

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param BuilderiusModulesProviderInterface $provider
     */
    public function addProvider(BuilderiusModulesProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getModules($templateType, $technology, $withSettings = true, $configVersions = [])
    {
        $modules = $this->cache->get(sprintf('builderius_%s_%s_composite_modules_%s_%s', $templateType, $technology, $withSettings, json_encode($configVersions)));
        if (false === $modules) {
            if (empty($this->modules) || !isset($this->modules[$templateType][$technology])) {
                foreach ($this->providers as $provider) {
                    if (!isset($this->modules[$templateType][$technology])) {
                        $this->modules[$templateType][$technology] = $provider->getModules($templateType, $technology, $withSettings, $configVersions);
                    } else {
                        $this->modules[$templateType][$technology] =
                            array_merge(
                                $this->modules[$templateType][$technology],
                                $provider->getModules($templateType, $technology, $withSettings, $configVersions)
                            );
                    }
                }
            }
            $modules = $this->modules[$templateType][$technology];
            $this->cache->set(sprintf('builderius_%s_%s_composite_modules_%s_%s', $templateType, $technology, $withSettings, json_encode($configVersions)), $modules);
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
        if (array_key_exists($name, $this->getModules($templateType, $technology, $withSettings, $configVersions))) {
            return true;
        }

        return false;
    }
}
