<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;

class AssetAwareBuilderiusContainerModuleDecorator extends BuilderiusContainerModuleDecorator implements AssetAwareInterface, InlineAssetAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(BuilderiusModuleInterface $module)
    {
        if (!$module instanceof BuilderiusContainerModuleInterface) {
            throw new \Exception(
                sprintf(
                    '%s can decorate only classes which implements %s',
                    self::class,
                    BuilderiusContainerModuleInterface::class
                )
            );
        }
        if (!$module instanceof AssetAwareInterface) {
            throw new \Exception(
                sprintf(
                    '%s can decorate only classes which implements %s',
                    self::class,
                    AssetAwareInterface::class
                )
            );
        }
        $this->module = $module;
    }

    /**
     * @inheritDoc
     */
    public function hasAssets()
    {
        return $this->module->hasAssets();
    }

    /**
     * @inheritDoc
     */
    public function getAssets()
    {
        return $this->module->getAssets();
    }

    /**
     * @inheritDoc
     */
    public function addAsset(AssetInterface $asset)
    {
        $this->module->addAsset($asset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAssets(array $assets)
    {
        $this->module->setAssets($assets);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasInlineAssets()
    {
        return $this->module->hasInlineAssets();
    }

    /**
     * @inheritDoc
     */
    public function getInlineAssets()
    {
        return $this->module->getInlineAssets();
    }

    /**
     * @inheritDoc
     */
    public function addInlineAsset(InlineAssetInterface $inlineAsset)
    {
        $this->module->addInlineAsset($inlineAsset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setInlineAssets(array $inlineAssets)
    {
        $this->module->setInlineAssets($inlineAssets);

        return $this;
    }
}