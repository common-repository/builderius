<?php

namespace Builderius\Bundle\CategoryBundle\Provider;

use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategoryInterface;

class StandardBuilderiusCategoriesProvider implements BuilderiusCategoriesProviderInterface
{
    /**
     * @var array
     */
    protected $categoriesByGroups = [];

    /**
     * @param BuilderiusCategoryInterface $category
     */
    public function addCategory(BuilderiusCategoryInterface $category)
    {
        foreach ($category->getGroups() as $group) {
            $this->categoriesByGroups[$group][sanitize_text_field($category->getName())] = $category;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories($group = null)
    {
        if ($group !== null) {
            if (isset($this->categoriesByGroups[$group])) {
                return $this->categoriesByGroups[$group];
            } else {
                return [];
            }
        }

        return $this->categoriesByGroups;
    }



    /**
     * {@inheritdoc}
     */
    public function getCategory($group, $name)
    {
        if ($this->hasCategory($group, $name)) {
            return $this->categoriesByGroups[$group][$name];
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCategory($group, $name)
    {
        if (isset($this->categoriesByGroups[$group][$name])) {
            return true;
        }

        return false;
    }
}
