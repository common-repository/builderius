<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\Script;

class ModuleConfigVersionsRelatedScript extends Script implements ModuleConfigVersionsRelatedAssetInterface
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