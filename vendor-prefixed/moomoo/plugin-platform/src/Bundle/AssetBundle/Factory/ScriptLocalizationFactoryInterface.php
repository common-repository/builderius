<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Factory;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface;
interface ScriptLocalizationFactoryInterface
{
    /**
     * @param string $object
     * @param string $property
     * @param array $data
     * @return ScriptLocalizationInterface
     */
    public static function create($object, $property, $data);
}
