<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Event;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;
class AssetsContainingEvent extends \Builderius\Symfony\Contracts\EventDispatcher\Event
{
    /**
     * @var AssetInterface[]
     */
    private $assets;
    /**
     * @param AssetInterface[] $assets
     */
    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }
    /**
     * @return AssetInterface[]
     */
    public function getAssets()
    {
        return $this->assets;
    }
    /**
     * @param AssetInterface[] $assets
     * @return $this
     */
    public function setAssets(array $assets)
    {
        $this->assets = $assets;
        return $this;
    }
}
