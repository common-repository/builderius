<?php

namespace Builderius\Bundle\ResponsiveBundle\Provider;

use Builderius\Bundle\ResponsiveBundle\Strategy\BuilderiusResponsiveStrategyInterface;

interface BuilderiusResponsiveStrategiesProviderInterface
{
    /**
     * @return BuilderiusResponsiveStrategyInterface[]
     */
    public function getResponsiveStrategies();

    /**
     * @param string $name
     * @return BuilderiusResponsiveStrategyInterface|null
     */
    public function getResponsiveStrategy($name);
    
    /**
     * @param string $name
     * @return bool
     */
    public function hasResponsiveStrategy($name);
}
