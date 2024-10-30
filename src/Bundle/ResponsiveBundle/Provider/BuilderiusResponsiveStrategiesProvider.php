<?php

namespace Builderius\Bundle\ResponsiveBundle\Provider;

use Builderius\Bundle\ResponsiveBundle\Strategy\BuilderiusResponsiveStrategyInterface;

class BuilderiusResponsiveStrategiesProvider implements BuilderiusResponsiveStrategiesProviderInterface
{
    /**
     * @var BuilderiusResponsiveStrategyInterface[]
     */
    private $responsiveStrategies = [];

    /**
     * @param BuilderiusResponsiveStrategyInterface $responsiveStrategy
     */
    public function addResponsiveStrategy(BuilderiusResponsiveStrategyInterface $responsiveStrategy)
    {
        $this->responsiveStrategies[$responsiveStrategy->getName()] = $responsiveStrategy;
    }
    
    /**
     * @inheritDoc
     */
    public function getResponsiveStrategies()
    {
        return $this->responsiveStrategies;
    }

    /**
     * @inheritDoc
     */
    public function getResponsiveStrategy($name)
    {
        if ($this->hasResponsiveStrategy($name)) {
            return $this->responsiveStrategies[$name];
        }
        
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasResponsiveStrategy($name)
    {
        return isset($this->responsiveStrategies[$name]);
    }
}
