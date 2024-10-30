<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
class FrontendScriptInlineAssetsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element\AbstractInlineAssetsRegistratorChainElement
{
    /**
     * @var string
     */
    protected $assetRegistrationFunction = 'wp_footer';
    /**
     * @var string
     */
    protected $registrationFunction = 'wp_enqueue_scripts';
    /**
     * @inheritDoc
     */
    public function isApplicable($assetType)
    {
        return 'script' === $assetType;
    }
    /**
     * @inheritDoc
     */
    public function enqueueDependency(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface $asset)
    {
        if (!empty($asset->getDependencies())) {
            $wp_scripts = wp_scripts();
            foreach ($asset->getDependencies() as $dependency) {
                if (\in_array($dependency, \array_keys($wp_scripts->registered))) {
                    $wp_scripts->enqueue($dependency);
                }
            }
        }
    }
    /**
     * @inheritDoc
     */
    public function registerAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface $asset)
    {
        echo $this->getFinalContent($asset);
    }
    /**
     * @param InlineAssetInterface $asset
     * @return string
     */
    protected function getFinalContent(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface $asset)
    {
        $htmlAttributes = '';
        if (!empty($asset->getAssetData())) {
            $groupedData = [];
            foreach ($asset->getAssetData() as $dataItem) {
                $groupedData[$dataItem->getGroup()][$dataItem->getKey()] = $dataItem->getValue();
            }
            if (isset($groupedData['htmlAttributes'])) {
                $htmlAttributes = $this->generateHtmlAttributes($groupedData['htmlAttributes']);
            }
        }
        return \sprintf('<script%s%s%s>%s</script>', $asset->getTagType() ? \sprintf(' type="%s"', $asset->getTagType()) : '', $asset->getId() ? \sprintf(' id="%s"', $asset->getId()) : '', $htmlAttributes === '' ? '' : \sprintf(' %s', $htmlAttributes), $asset->getContent());
    }
}
