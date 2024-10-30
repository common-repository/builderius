<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Event;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;
class InlineAssetsContainingEvent extends \Builderius\Symfony\Contracts\EventDispatcher\Event
{
    /**
     * @var InlineAssetInterface[]
     */
    private $assets;
    /**
     * @param InlineAssetInterface[] $assets
     */
    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }
    /**
     * @return InlineAssetInterface[]
     */
    public function getAssets()
    {
        return $this->assets;
    }
    /**
     * @param InlineAssetInterface[] $assets
     * @return $this
     */
    public function setAssets(array $assets)
    {
        $this->assets = $assets;
        return $this;
    }
}
