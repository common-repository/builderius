<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
class InlineAssetsRegistry implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry\InlineAssetsRegistryInterface
{
    /**
     * @var InlineAssetInterface[]
     */
    private $assets = [];
    /**
     * @param InlineAssetInterface $script
     */
    public function addAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface $asset)
    {
        $this->assets[$asset->getCategory()][] = $asset;
    }
    /**
     * @inheritDoc
     */
    public function getAssets($category)
    {
        return isset($this->assets[$category]) ? $this->assets[$category] : [];
    }
}
