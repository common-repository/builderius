<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareTrait;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetAwareTrait;

class AssetAwareBuilderiusModule extends BuilderiusModule implements AssetAwareInterface, InlineAssetAwareInterface
{
    use AssetAwareTrait;
    use InlineAssetAwareTrait;
}