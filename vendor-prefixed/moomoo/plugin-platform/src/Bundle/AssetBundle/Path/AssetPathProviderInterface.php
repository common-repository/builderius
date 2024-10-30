<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Path;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
interface AssetPathProviderInterface
{
    /**
     * @param AssetInterface $asset
     * @return string|null
     */
    public function getAssetPath(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset);
}
