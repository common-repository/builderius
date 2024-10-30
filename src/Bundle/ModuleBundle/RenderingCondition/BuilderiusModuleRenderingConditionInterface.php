<?php

namespace Builderius\Bundle\ModuleBundle\RenderingCondition;

interface BuilderiusModuleRenderingConditionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);
    /**
     * @return string
     */
    public function getExpression();

    /**
     * @param string $expression
     * @return $this
     */
    public function setExpression($expression);

    /**
     * @return array
     */
    public function getGraphQLPath();

    /**
     * @param array $graphqlPath
     * @return $this
     */
    public function setGraphQLPath(array $graphqlPath);

    /**
     * @return array|null
     */
    public function getOperators();

    /**
     * @param array $operators
     * @return $this
     */
    public function setOperators(array $operators);

    /**
     * @return string|null
     */
    public function getWidgetType();

    /**
     * @param string $widgetType
     * @return $this
     */
    public function setWidgetType($widgetType);

    /**
     * @return string|null
     */
    public function getPlaceholder();

    /**
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder);

    /**
     * @return array|null
     */
    public function getValueList();

    /**
     * @param array $valueList
     * @return $this
     */
    public function setValueList(array $valueList);
}