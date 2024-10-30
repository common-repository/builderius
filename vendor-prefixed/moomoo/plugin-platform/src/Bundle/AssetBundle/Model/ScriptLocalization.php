<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
class ScriptLocalization extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface
{
    const OBJECT_NAME_FIELD = 'object_name';
    const PROPERTY_NAME_FIELD = 'property_name';
    const PROPERTY_DATA_FIELD = 'property_data';
    /**
     * @inheritDoc
     */
    public function getObjectName()
    {
        return $this->get(self::OBJECT_NAME_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getPropertyName()
    {
        return $this->get(self::PROPERTY_NAME_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        return $this->get(self::PROPERTY_DATA_FIELD);
    }
}
