<?php

namespace Builderius\Bundle\ModuleBundle\RenderingCondition;

interface BuilderiusModuleRenderingConditionsProviderInterface
{
    /**
     * @return BuilderiusModuleRenderingConditionInterface[]
     */
    public function getRenderingConditions();

    /**
     * @param string $name
     * @return BuilderiusModuleRenderingConditionInterface|null
     */
    public function getRenderingCondition($name);
}