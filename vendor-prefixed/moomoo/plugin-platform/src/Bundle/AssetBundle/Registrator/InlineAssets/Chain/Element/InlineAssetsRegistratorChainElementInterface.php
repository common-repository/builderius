<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
interface InlineAssetsRegistratorChainElementInterface
{
    const ASSETS_TYPES = ['script', 'style'];
    /**
     * @param string $assetType
     * @return bool
     */
    public function isApplicable($assetType);
    /**
     * @param InlineAssetInterface $asset
     */
    public function enqueueDependency(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface $asset);
    /**
     * @param InlineAssetInterface $asset
     */
    public function registerAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface $asset);
}
