<?php

namespace Builderius\Bundle\ModuleBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;
use Builderius\Bundle\ModuleBundle\RenderingCondition\BuilderiusModuleRenderingCondition;
use Builderius\Bundle\ModuleBundle\RenderingCondition\BuilderiusModuleRenderingConditionInterface;
use Builderius\Bundle\ModuleBundle\RenderingCondition\BuilderiusModuleRenderingConditionsProviderInterface;

class BuilderiusModuleRenderingConditionsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'moduleRenderingConditions';

    /**
     * @var BuilderiusModuleRenderingConditionsProviderInterface
     */
    private $conditionsProvider;

    /**
     * @param BuilderiusModuleRenderingConditionsProviderInterface $conditionsProvider
     */
    public function __construct(
        BuilderiusModuleRenderingConditionsProviderInterface $conditionsProvider
    ) {
        $this->conditionsProvider = $conditionsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->conditionsProvider->getRenderingConditions() as $condition) {
            $data[$condition->getCategory()][$condition->getName()] = $this->getConditionConfig($condition);
        }
        
        return $data;
    }

    /**
     * @param BuilderiusModuleInterface $module
     * @return array
     * @throws \Exception
     */
    private function getConditionConfig(BuilderiusModuleRenderingConditionInterface $condition)
    {
        $config = [
            BuilderiusModuleRenderingCondition::NAME_FIELD => $condition->getName(),
            BuilderiusModuleRenderingCondition::LABEL_FIELD => $condition->getLabel(),
            BuilderiusModuleRenderingCondition::CATEGORY_FIELD => $condition->getCategory(),
            BuilderiusModuleRenderingCondition::SORT_ORDER_FIELD => $condition->getSortOrder(),
            BuilderiusModuleRenderingCondition::OPERATORS_FIELD => $condition->getOperators() ? : ['select_equals', 'select_not_equals'],
            BuilderiusModuleRenderingCondition::EXPRESSION_FIELD => $condition->getExpression(),
            BuilderiusModuleRenderingCondition::GRAPHQL_PATH_FIELD => $condition->getGraphQLPath(),
            'type' => $condition->getWidgetType() ? : 'select',
            'preferWidgets' => [$condition->getWidgetType() ? : 'select'],
            'valueSources' => ['value'],
            'valuePlaceholder' => $condition->getPlaceholder() ? : '',
            BuilderiusModuleRenderingCondition::VALUE_LIST_FIELD => $condition->getValueList()
        ];

        return $config;
    }
}
