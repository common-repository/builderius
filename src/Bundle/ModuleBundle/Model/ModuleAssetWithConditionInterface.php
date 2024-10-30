<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;

interface ModuleAssetWithConditionInterface extends AssetInterface
{
    /**
     * @return string
     */
    public function getConditionExpression();

    /**
     * @return bool
     */
    public function loadIfEmptyContext();
}