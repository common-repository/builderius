<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;

interface ModuleConfigVersionsRelatedAssetInterface extends AssetInterface
{
    /**
     * @return array
     */
    public function getConfigVersions();
}