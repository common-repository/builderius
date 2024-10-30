<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Factory;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface;
interface InlineAssetFactoryInterface
{
    /**
     * @param array $arguments
     * @param ConditionInterface[] $conditions
     * @return InlineAssetInterface
     */
    public static function create(array $arguments, array $conditions = []);
}
