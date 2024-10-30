<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

class Style extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AbstractAsset implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\StyleInterface
{
    const MEDIA_FIELD = 'media';
    /**
     * @inheritDoc
     */
    public function getMedia()
    {
        return $this->get(self::MEDIA_FIELD, 'all');
    }
}
