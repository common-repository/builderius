<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
class AssetsRegistry implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry\AssetsRegistryInterface
{
    /**
     * @var AssetInterface[]
     */
    private $assets = [];
    /**
     * @param AssetInterface $asset
     */
    public function addAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
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
