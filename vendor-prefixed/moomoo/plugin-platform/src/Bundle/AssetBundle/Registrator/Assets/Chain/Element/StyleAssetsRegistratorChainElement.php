<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\StyleInterface;
class StyleAssetsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element\AbstractAssetsRegistratorChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        return $asset instanceof \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\StyleInterface;
    }
    /**
     * @inheritDoc
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        /** @var StyleInterface $asset */
        if ($asset->registerOnly()) {
            wp_register_style($asset->getHandle(), $this->pathProvider->getAssetPath($asset), $asset->getDependencies(), $asset->getVersion(), $asset->getMedia() ?: 'all');
        } else {
            wp_enqueue_style($asset->getHandle(), $this->pathProvider->getAssetPath($asset), $asset->getDependencies(), $asset->getVersion(), $asset->getMedia() ?: 'all');
        }
        if (!empty($asset->getAssetData())) {
            $groupedData = [];
            foreach ($asset->getAssetData() as $dataItem) {
                $groupedData[$dataItem->getGroup()][$dataItem->getKey()] = $dataItem->getValue();
            }
            foreach ($groupedData as $group => $values) {
                wp_style_add_data($asset->getHandle(), $group, $values);
            }
        }
    }
}
