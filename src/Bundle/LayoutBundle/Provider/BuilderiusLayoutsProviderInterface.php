<?php

namespace Builderius\Bundle\LayoutBundle\Provider;

use Builderius\Bundle\LayoutBundle\Model\BuilderiusLayoutInterface;

interface BuilderiusLayoutsProviderInterface
{
    /**
     * @param string $technology
     * @return BuilderiusLayoutInterface[]
     */
    public function getLayouts($technology);

    /**
     * @param $name
     * @param string $technology
     * @return BuilderiusLayoutInterface|null
     */
    public function getLayout($name, $technology);
    
    /**
     * @param $name
     * @param string $technology
     * @return bool
     */
    public function hasLayout($name, $technology);
}
