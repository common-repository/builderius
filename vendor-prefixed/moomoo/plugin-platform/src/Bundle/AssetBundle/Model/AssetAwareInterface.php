<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

interface AssetAwareInterface
{
    /**
     * @return bool
     */
    public function hasAssets();
    /**
     * @return AssetInterface[]
     */
    public function getAssets();
    /**
     * @param AssetInterface $asset
     * @return $this
     */
    public function addAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset);
    /**
     * @param AssetInterface[] $assets
     * @return $this
     */
    public function setAssets(array $assets);
}
