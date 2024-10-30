<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
interface AssetsRegistratorChainElementInterface
{
    /**
     * @param AssetInterface $asset
     * @return bool
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset);
    /**
     * @param AssetInterface $asset
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset);
}
