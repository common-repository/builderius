<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig\Chain\Element;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;

class BuilderiusTemplateContentConfigCheckerModuleExistenceChainElement extends
AbstractBuilderiusTemplateContentConfigCheckerChainElement
{
    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @param BuilderiusModulesProviderInterface $modulesProvider
     */
    public function __construct(BuilderiusModulesProviderInterface $modulesProvider)
    {
        $this->modulesProvider = $modulesProvider;
    }

    /**
     * @inheritDoc
     */
    protected function evaluate(array $configItem, $templateType, $templateTechnology)
    {
        if (!$this->modulesProvider->hasModule($configItem['name'], $templateType, $templateTechnology)) {
            throw new \Exception(
                sprintf(
                    'There is no registered module with name "%s"',
                    $configItem['name']
                )
            );
        }

        return true;
    }
}
