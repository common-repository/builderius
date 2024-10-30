<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
abstract class AbstractAsset extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface, \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface
{
    use ConditionAwareTrait;
    const REGISTER_ONLY = 'registerOnly';
    const HANDLE_FIELD = 'handle';
    const SOURCE_FIELD = 'source';
    const VERSION_FIELD = 'version';
    const CATEGORY_FIELD = 'category';
    const DEPENDENCIES_FIELD = 'dependencies';
    const ASSET_DATA_FIELD = 'data';
    /**
     * @inheritDoc
     */
    public function registerOnly()
    {
        return $this->get(self::REGISTER_ONLY, \false);
    }
    /**
     * @inheritDoc
     */
    public function getHandle()
    {
        return $this->get(self::HANDLE_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getSource()
    {
        return $this->get(self::SOURCE_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return $this->get(self::DEPENDENCIES_FIELD, []);
    }
    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return $this->get(self::VERSION_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getAssetData()
    {
        return $this->get(self::ASSET_DATA_FIELD, []);
    }
    /**
     * @inheritDoc
     */
    public function addAssetDataItem(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItemInterface $dataItem)
    {
        $assetData = $this->getAssetData();
        if (!\in_array($dataItem, $assetData)) {
            $assetData[$dataItem->getKey()] = $dataItem;
            $this->set(self::ASSET_DATA_FIELD, $assetData);
        }
        return $this;
    }
}
