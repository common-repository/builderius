<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
class InlineAsset extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface, \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface
{
    use ConditionAwareTrait;
    const TYPE_FIELD = 'type';
    const TAG_TYPE_FIELD = 'tagType';
    const ID_FIELD = 'id';
    const CATEGORY_FIELD = 'category';
    const CONTENT_FIELD = 'content';
    const DEPENDENCIES_FIELD = 'dependencies';
    const ASSET_DATA_FIELD = 'assetData';
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getTagType()
    {
        return $this->get(self::TAG_TYPE_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function setTagType($tagType)
    {
        $this->set(self::TAG_TYPE_FIELD, $tagType);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->get(self::CONTENT_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->set(self::CONTENT_FIELD, $content);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->get(self::ID_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->set(self::ID_FIELD, $id);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD, \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface::FRONTEND_CATEGORY);
    }
    /**
     * @inheritDoc
     */
    public function setCategory($category)
    {
        $this->set(self::CATEGORY_FIELD, $category);
        return $this;
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
    public function setDependencies(array $dependencies)
    {
        $this->set(self::DEPENDENCIES_FIELD, $dependencies);
        return $this;
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
