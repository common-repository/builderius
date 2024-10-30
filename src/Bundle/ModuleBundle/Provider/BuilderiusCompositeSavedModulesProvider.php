<?php

namespace Builderius\Bundle\ModuleBundle\Provider;

use Builderius\Bundle\ModuleBundle\Factory\BuilderiusSavedCompositeModuleFromPostFactory;
use Builderius\Bundle\ModuleBundle\Registration\BuilderiusSavedCompositeModulePostType;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;

class BuilderiusCompositeSavedModulesProvider implements BuilderiusModulesProviderInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->wpQuery = $wpQuery;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getModules($templateType, $technology, $withSettings = true, $configVersions = [])
    {
        $cacheKey = sprintf('builderius_%s_%s_saved_composite_modules_%s_%s', $templateType, $technology, $withSettings, json_encode($configVersions));
        $modules = $this->cache->get($cacheKey);
        if (false === $modules) {
            $queryArgs = [
                'post_type' => BuilderiusSavedCompositeModulePostType::POST_TYPE,
                'post_status' => get_post_stati(),
                'posts_per_page' => -1,
                'no_found_rows' => true,
            ];
            $queryArgs['tax_query'][] = [
                'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                'field' => 'slug',
                'include_children' => false,
                'terms' => [$technology]
            ];
            $posts = $this->wpQuery->query($queryArgs);
            $modules = [];
            foreach ($posts as $post) {
                $modules[] = BuilderiusSavedCompositeModuleFromPostFactory::createSavedCompositeModule($post);
            }
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
