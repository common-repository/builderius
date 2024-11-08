<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
class AssetDataItem extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItemInterface
{
    const KEY_FIELD = 'key';
    const VALUE_FIELD = 'value';
    const GROUP_FIELD = 'group';
    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->get(self::KEY_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->get(self::VALUE_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return $this->get(self::GROUP_FIELD);
    }
}
