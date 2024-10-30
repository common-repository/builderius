<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\Chain\Element;

use InvalidArgumentException;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\StyleInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
class BundleAssetPathProvider extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\Chain\Element\AbstractAssetPathProviderChainElement
{
    /**
     * @var array
     */
    private $bundles = [];
    /**
     * @param ContainerInterface $container
     */
    public function __construct(\Builderius\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->bundles = $container->get('kernel')->getBundles();
    }
    /**
     * @inheritDoc
     */
    public function getAssetPath(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        $subFolder = null;
        if ($asset instanceof \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptInterface) {
            $subFolder = 'js';
        } elseif ($asset instanceof \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\StyleInterface) {
            $subFolder = 'css';
        }
        if ($subFolder === null) {
            throw new \InvalidArgumentException('Not correct asset type');
        }
        $plugins_url = untrailingslashit(plugins_url('/'));
        $plugins_path = ABSPATH . 'wp-content/plugins';
        $assetPathParts = \explode(':', $asset->getSource());
        if (isset($this->bundles[$assetPathParts[0]])) {
            $bundleFullPath = $this->bundles[$assetPathParts[0]]->getPath();
            $bundleRelativePath = \explode('plugins/', $bundleFullPath)[1];
            $relativePath = \sprintf('%s/Resources/assets/%s/%s', $bundleRelativePath, $subFolder, $assetPathParts[1]);
            $absolutePath = \sprintf('%s/%s', $plugins_path, $relativePath);
            if (\file_exists($absolutePath)) {
                return \sprintf('%s/%s', $plugins_url, $relativePath);
            } elseif ($this->getSuccessor()) {
                return $this->getSuccessor()->getAssetPath($asset);
            }
            return null;
        } elseif ($this->getSuccessor()) {
            return $this->getSuccessor()->getAssetPath($asset);
        }
        return null;
    }
}
