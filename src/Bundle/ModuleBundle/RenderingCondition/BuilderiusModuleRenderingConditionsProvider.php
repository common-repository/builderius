<?php

namespace Builderius\Bundle\ModuleBundle\RenderingCondition;

class BuilderiusModuleRenderingConditionsProvider implements BuilderiusModuleRenderingConditionsProviderInterface
{
    /**
     * @var BuilderiusModuleRenderingConditionInterface[]
     */
    private $renderingConditions = [];

    /**
     * @param BuilderiusModuleRenderingConditionInterface $condition
     * @return $this
     */
    public function addRenderingCondition(BuilderiusModuleRenderingConditionInterface $condition)
    {
        $this->renderingConditions[$condition->getName()] = $condition;

        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getRenderingConditions()
    {
        return $this->renderingConditions;
    }

    /**
     * @inheritDoc
     */
    public function getRenderingCondition($name)
    {
        return isset($this->renderingConditions[$name]) ? $this->renderingConditions[$name] : null;
    }
}