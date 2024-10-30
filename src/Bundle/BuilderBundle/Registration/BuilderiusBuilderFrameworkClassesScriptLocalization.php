<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registry\BuilderiusCssFrameworksRegistryInterface;

class BuilderiusBuilderFrameworkClassesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'cssFrameworkClasses';

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
        $classes = [];
        foreach ($this->cssFameworksRegistry->getFrameworks() as $framework) {
            $frameworkClasses = $framework->getClasses();
            if (!empty($frameworkClasses)) {
                $classes[$framework->getName()] = $frameworkClasses;
            }
        }

        return $classes;
    }
}
