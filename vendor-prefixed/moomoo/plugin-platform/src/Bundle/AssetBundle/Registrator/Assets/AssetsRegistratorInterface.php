<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
interface AssetsRegistratorInterface
{
    /**
     * @param AssetInterface[] $assets
     * @return mixed
     */
    public function registerAssets(array $assets);
    /**
     * @param AssetInterface $asset
     */
    public function registerAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset);
}
