<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registry\BuilderiusCssFrameworksRegistryInterface;

class BuilderiusBuilderFrameworkVariablesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'cssFrameworkVariables';

    /**
     * @var BuilderiusCssFrameworksRegistryInterface
     */
    private $cssFameworksRegistry;

    /**
     * @param BuilderiusCssFrameworksRegistryInterface $cssFameworksRegistry
     */
    public function __construct(
        BuilderiusCssFrameworksRegistryInterface $cssFameworksRegistry
    ) {
        $this->cssFameworksRegistry = $cssFameworksRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $variables = [];
        foreach ($this->cssFameworksRegistry->getFrameworks() as $framework) {
            $frameworkVariables = $framework->getVariables();
            if (!empty($frameworkVariables)) {
                $variables[$framework->getName()] = $frameworkVariables;
            }
        }

        return $variables;
    }
}
