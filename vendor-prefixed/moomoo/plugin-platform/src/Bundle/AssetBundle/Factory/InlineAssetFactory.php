<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Factory;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset;
class InlineAssetFactory implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Factory\InlineAssetFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create(array $arguments, array $conditions = [])
    {
        $script = new \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset($arguments);
        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                $script->addCondition($condition);
            }
        }
        return $script;
    }
}
