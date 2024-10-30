<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
interface InlineAssetsRegistratorInterface
{
    /**
     * @param InlineAssetInterface[] $assets
     */
    public function registerAssets(array $assets);
}
