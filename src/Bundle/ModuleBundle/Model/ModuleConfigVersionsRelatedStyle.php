<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\Style;

class ModuleConfigVersionsRelatedStyle extends Style implements ModuleConfigVersionsRelatedAssetInterface
{
    const CONFIG_VERSIONS_FIELD = 'config_versions';

    /**
     * @inheritDoc
     */
    public function getConfigVersions()
    {
        return $this->get(self::CONFIG_VERSIONS_FIELD);
    }
}