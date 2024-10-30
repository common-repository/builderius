<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry\BuilderiusTemplateApplyRuleCategoriesRegistryInterface;

class BuilderiusTemplateApplyRulesCategoriesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'applyRulesCategories';

    /**
     * @var BuilderiusTemplateApplyRuleCategoriesRegistryInterface
     */
    private $templateApplyRuleCategoriesRegistry;

    /**
     * @param BuilderiusTemplateApplyRuleCategoriesRegistryInterface $templateApplyRuleCategoriesRegistry
     */
    public function __construct(
        BuilderiusTemplateApplyRuleCategoriesRegistryInterface $templateApplyRuleCategoriesRegistry
    ) {
        $this->templateApplyRuleCategoriesRegistry = $templateApplyRuleCategoriesRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $result = [];
        $categories = $this->templateApplyRuleCategoriesRegistry->getCategories();
        usort(
            $categories,
            function (BuilderiusTemplateApplyRuleCategoryInterface $a, BuilderiusTemplateApplyRuleCategoryInterface $b) {
                if ($a->getSortOrder() === $b->getSortOrder()) return 0;
                return ($a->getSortOrder() < $b->getSortOrder()) ? -1 : 1;
            });
        foreach ($categories as $category) {
            $result[] = [
                'name' => $category->getName(),
                'label' => $category->getLabel(),
                'description' => $category->getDescription(),
            ];
        }
        
        return $result;
    }
}
