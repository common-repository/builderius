<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Factory;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalization;
class ScriptLocalizationFactory implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Factory\ScriptLocalizationFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create($objectName, $propertyName, $propertyData)
    {
        return new \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalization([\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalization::OBJECT_NAME_FIELD => $objectName, \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalization::PROPERTY_NAME_FIELD => $propertyName, \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalization::PROPERTY_DATA_FIELD => $propertyData]);
    }
}
