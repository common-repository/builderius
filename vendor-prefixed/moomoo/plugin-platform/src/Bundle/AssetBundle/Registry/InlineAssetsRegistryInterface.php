<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
interface InlineAssetsRegistryInterface
{
    /**
     * @param string $category
     * @return InlineAssetInterface[]
     */
    public function getAssets($category);
}
