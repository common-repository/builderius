<?php

namespace Builderius\Bundle\ModuleBundle\RenderingCondition;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\ApplyRuleArgumentsProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusModuleRenderingCondition extends ParameterBag implements BuilderiusModuleRenderingConditionInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const CATEGORY_FIELD = 'category';
    const SORT_ORDER_FIELD = 'sortOrder';
    const EXPRESSION_FIELD = 'expression';
    const GRAPHQL_PATH_FIELD = 'graphqlPath';
    const OPERATORS_FIELD = 'operators';
    const WIDGET_TYPE_FIELD = 'widgetType';
    const PLACEHOLDER_FIELD = 'placeholder';
    const VALUE_LIST_FIELD = 'valueList';
    const VALUE_LIST_PROVIDER_FIELD = 'valueListProvider';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->get(self::LABEL_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setCategory($category)
    {
        $this->set(self::CATEGORY_FIELD, $category);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 10);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::SORT_ORDER_FIELD, $sortOrder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpression()
    {
        return $this->get(self::EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setExpression($expression)
    {
        $this->set(self::EXPRESSION_FIELD, $expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGraphQLPath()
    {
        return $this->get(self::GRAPHQL_PATH_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setGraphQLPath(array $graphqlPath)
    {
        $this->set(self::GRAPHQL_PATH_FIELD, $graphqlPath);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return $this->get(self::OPERATORS_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setOperators(array $operators)
    {
        $this->set(self::OPERATORS_FIELD, $operators);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWidgetType()
    {
        return $this->get(self::WIDGET_TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setWidgetType($widgetType)
    {
        $this->set(self::WIDGET_TYPE_FIELD, $widgetType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPlaceholder()
    {
        return $this->get(self::PLACEHOLDER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setPlaceholder($placeholder)
    {
        $this->set(self::PLACEHOLDER_FIELD, $placeholder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueList()
    {
        if ($possibleArguments = $this->get(self::VALUE_LIST_FIELD)) {
            return $possibleArguments;
        } elseif ($possibleArgumentsProvider = $this->get(self::VALUE_LIST_PROVIDER_FIELD)) {
            return $possibleArgumentsProvider->getArguments();
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function setValueList(array $valueList)
    {
        $this->set(self::VALUE_LIST_FIELD, $valueList);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValueListProvider(ApplyRuleArgumentsProviderInterface $valueListProvider)
    {
        $this->set(self::VALUE_LIST_PROVIDER_FIELD, $valueListProvider);

        return $this;
    }
}