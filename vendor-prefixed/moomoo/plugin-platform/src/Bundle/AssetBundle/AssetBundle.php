<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\DependencyInjection\CompilerPass\ScriptLocalizationsCompilerPass;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AbstractAsset;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\AssetsRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\InlineAssetsRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry\AssetsRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Registry\InlineAssetsRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class AssetBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_inline_asset', 'moomoo_asset.registry.inline_assets', 'addAsset'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_asset', 'moomoo_asset.registry.assets', 'addAsset'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\AssetBundle\DependencyInjection\CompilerPass\ScriptLocalizationsCompilerPass());
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var AssetsRegistryInterface $assetsRegistry */
        $assetsRegistry = $this->container->get('moomoo_asset.registry.assets');
        /** @var InlineAssetsRegistryInterface $inlineAssetsRegistry */
        $inlineAssetsRegistry = $this->container->get('moomoo_asset.registry.inline_assets');
        if (is_admin()) {
            /** @var AssetsRegistratorInterface $adminAssetsRegistrator */
            $adminAssetsRegistrator = $this->container->get('moomoo_asset.registrator.admin');
            $adminAssetsRegistrator->registerAssets($assetsRegistry->getAssets(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AbstractAsset::ADMIN_CATEGORY));
            /** @var InlineAssetsRegistratorInterface $adminInlineAssetsRegistrator */
            $adminInlineAssetsRegistrator = $this->container->get('moomoo_asset.registrator.inline_assets.admin');
            $adminInlineAssetsRegistrator->registerAssets($inlineAssetsRegistry->getAssets(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface::ADMIN_CATEGORY));
        } else {
            /** @var AssetsRegistratorInterface $frontendAssetsRegistrator */
            $frontendAssetsRegistrator = $this->container->get('moomoo_asset.registrator.frontend');
            $frontendAssetsRegistrator->registerAssets($assetsRegistry->getAssets(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AbstractAsset::FRONTEND_CATEGORY));

            /** @var InlineAssetsRegistratorInterface $frontendInlineAssetsRegistrator */
            $frontendInlineAssetsRegistrator = $this->container->get('moomoo_asset.registrator.inline_assets.frontend');
            $frontendInlineAssetsRegistrator->registerAssets($inlineAssetsRegistry->getAssets(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface::FRONTEND_CATEGORY));
        }
        parent::boot();
    }
}
