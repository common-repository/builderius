<?php

namespace Builderius\Bundle\CategoryBundle\Provider;

use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategoryInterface;

interface BuilderiusCategoriesProviderInterface
{
    /**
     * @param string|null $group
     * @return BuilderiusCategoryInterface[]
     */
    public function getCategories($group = null);

    /**
     * @param string $group
     * @param string $name
     * @return BuilderiusCategoryInterface
     */
    public function getCategory($group, $name);

    /**
     * @param string $group
     * @param string $name
     * @return bool
     */
    public function hasCategory($group, $name);
}
