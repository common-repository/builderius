<?php

namespace Builderius\Bundle\CategoryBundle\Provider;

use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategoryInterface;

class CompositeBuilderiusCategoriesProvider implements BuilderiusCategoriesProviderInterface
{
    /**
     * @var BuilderiusCategoriesProviderInterface[]
     */
    private $providers = [];

    /**
     * @var array
     */
    private $categoriesByGroups = [];

    /**
     * @param BuilderiusCategoriesProviderInterface $provider
     */
    public function addProvider(BuilderiusCategoriesProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @inheritDoc
     */
    public function getCategories($group = null)
    {
        if ($group !== null) {
            $categories = [];
            foreach ($this->providers as $provider) {
                $categories = array_merge($categories, $provider->getCategories($group));
            }

            return $this->sortCategories($categories);
        } else {
            if (empty($this->categoriesByGroups)) {
                $categoriesByGroups = [];
                foreach ($this->providers as $provider) {
                    foreach ($provider->getCategories() as $group => $groupCategories) {
                        if (isset($categoriesByGroups[$group])) {
                            $categoriesByGroups[$group] = array_merge($categoriesByGroups[$group], $groupCategories);
                        } else {
                            $categoriesByGroups[$group] = $groupCategories;
                        }
                    }
                }
                foreach ($categoriesByGroups as $group => $groupCategories) {
                    $categoriesByGroups[$group] = $this->sortCategories($groupCategories);
                }
                $this->categoriesByGroups = $categoriesByGroups;
            }

            return $this->categoriesByGroups;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCategory($group, $name)
    {
        foreach ($this->providers as $provider) {
            if ($provider->hasCategory($group, $name)) {
                return $provider->getCategory($group, $name);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasCategory($group, $name)
    {
        foreach ($this->providers as $provider) {
            if ($provider->hasCategory($group, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param BuilderiusCategoryInterface[] $categories
     * @return BuilderiusCategoryInterface[]
     */
    protected function sortCategories(array $categories)
    {
        uasort($categories, function (BuilderiusCategoryInterface $a, BuilderiusCategoryInterface $b) {
            $aSortOrder = $a->getSortOrder();
            $bSortOrder = $b->getSortOrder();
            if ($aSortOrder < $bSortOrder) {
                return -1;
            } elseif ($aSortOrder > $bSortOrder) {
                return 1;
            } else {
                return 0;
            }
        });

        return $categories;
    }
}