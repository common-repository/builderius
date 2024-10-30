<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;

interface DynamicDataHelperInterface extends ConditionAwareInterface
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
    public function getCategory();

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category);

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
     * @return string
     */
    public function getExpression();

    /**
     * @param string $expression
     * @return $this
     */
    public function setExpression($expression);

    /**
     * @return bool
     */
    public function isEscaped();

    /**
     * @param bool $isEscaped
     * @return $this
     */
    public function setIsEscaped($isEscaped = true);

    /**
     * @return DynamicDataHelperArgumentInterface[]
     */
    public function getArguments();

    /**
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments);

    /**
     * @param DynamicDataHelperArgumentInterface $argument
     * @return $this
     */
    public function addArgument(DynamicDataHelperArgumentInterface $argument);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);
}