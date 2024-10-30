<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
class AbsoluteAssetPathProvider extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\Chain\Element\AbstractAssetPathProviderChainElement
{
    /**
     * @inheritDoc
     */
    public function getAssetPath(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        if ($asset->getSource()) {
            return $asset->getSource();
        } elseif ($this->getSuccessor()) {
            return $this->getSuccessor()->getAssetPath($asset);
        }
        return null;
    }
}
