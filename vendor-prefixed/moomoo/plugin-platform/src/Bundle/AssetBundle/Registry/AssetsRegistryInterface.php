<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
interface AssetsRegistryInterface
{
    /**
     * @param string $category
     * @return AssetInterface[]
     */
    public function getAssets($category);
}
