<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\ApplyRule\Starter\Registry\BuilderiusTemplateApplyRuleStartersRegistryInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateSubType\BuilderiusTemplateSubTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusTemplateApplyRulesStartersScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'applyRulesStarters';

    /**
     * @var BuilderiusTemplateApplyRuleStartersRegistryInterface
     */
    private $templateApplyRuleStartersRegistry;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusTemplateSubTypesProviderInterface
     */
    private $templateSubTypesProvider;

    /**
     * @param BuilderiusTemplateApplyRuleStartersRegistryInterface $templateApplyRuleStartersRegistry
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusTemplateSubTypesProviderInterface $templateSubTypesProvider
     */
    public function __construct(
        BuilderiusTemplateApplyRuleStartersRegistryInterface $templateApplyRuleStartersRegistry,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusTemplateSubTypesProviderInterface $templateSubTypesProvider
    ) {
        $this->templateApplyRuleStartersRegistry = $templateApplyRuleStartersRegistry;
        $this->templateTypesProvider = $templateTypesProvider;
        $this->templateSubTypesProvider = $templateSubTypesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $result = [];
        $starters = $this->templateApplyRuleStartersRegistry->getStarters();
        foreach ($starters as $starter){
            if ($starter->isValid()) {
                foreach ($starter->getTechnologies() as $technology) {
                    if (!isset($result[$technology])) {
                        $result[$technology] = [];
                    }
                    foreach ($starter->getTemplateTypes() as $type) {
                        $templateType = $this->templateTypesProvider->getType($type);
                        if ($templateType && $templateType->getName() === 'template') {
                            $subTypes = $this->templateSubTypesProvider->getSubTypes($templateType->getName());
                            foreach ($subTypes as $subType) {
                                $type = $subType->getName();
                                if (!isset($result[$technology][$type])) {
                                    $result[$technology][$type] = [];
                                }
                                $result[$technology][$type][$starter->getCategory()][$starter->getName()] = [
                                    'title' => $starter->getTitle(),
                                    'cfg' => $starter->getConfig()
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        return $result;
    }
}
