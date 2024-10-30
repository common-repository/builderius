<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\AssetPathProviderInterface;
abstract class AbstractAssetPathProviderChainElement implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\AssetPathProviderInterface
{
    /**
     * @var AssetPathProviderInterface|null
     */
    private $successor;
    /**
     * @param AssetPathProviderInterface $pathProvider
     */
    public function setSuccessor(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\AssetPathProviderInterface $pathProvider)
    {
        $this->successor = $pathProvider;
    }
    /**
     * @return AssetPathProviderInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }
}
