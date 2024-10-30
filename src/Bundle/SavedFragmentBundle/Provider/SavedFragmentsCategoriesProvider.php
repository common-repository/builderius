<?php

namespace Builderius\Bundle\SavedFragmentBundle\Provider;

use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentCategoryTaxonomy;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\CategoryBundle\Factory\BuilderiusCategoryFromTermFactory;

class SavedFragmentsCategoriesProvider implements BuilderiusCategoriesProviderInterface
{
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
     * @inheritDoc
     */
    public function getCategories($group = null)
    {
        $categories = $this->cache->get('builderius_sf_categories');
        if (false === $categories) {
            $categories = [];
            $sfCategoriesTerms = get_terms( [
                'taxonomy' => BuilderiusSavedFragmentCategoryTaxonomy::NAME,
                'hide_empty' => false,
            ] );
            foreach ($sfCategoriesTerms as $term) {
                $category = BuilderiusCategoryFromTermFactory::createCategory($term);
                $category->setGroups(['saved_fragment']);
                if (!$group) {
                    $categories['saved_fragment'][$term->name] = $category;
                } else {
                    $categories[$term->name] = $category;
                }


            }

            $this->cache->set('builderius_sf_categories', $categories);
        }

        return $categories;
    }

    /**
     * @inheritDoc
     */
    public function getCategory($group, $name)
    {
        if ($this->hasCategory($group, $name)) {
            return $this->getCategories()[$group][$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasCategory($group, $name)
    {
        if (isset($this->getCategories()[$group][$name])) {
            return true;
        }

        return false;
    }
}