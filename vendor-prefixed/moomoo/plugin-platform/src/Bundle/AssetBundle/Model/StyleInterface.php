<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

interface StyleInterface extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface
{
    /**
     * @return string|null
     */
    public function getMedia();
}
