<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class DynamicDataHelper extends ParameterBag implements DynamicDataHelperInterface
{
    use ConditionAwareTrait;

    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const CATEGORY_FIELD = 'category';
    const SORT_ORDER_FIELD = 'sortOrder';
    const EXPRESSION_FIELD = 'expression';
    const ESCAPED_FIELD = 'escaped';
    const TYPE_FIELD = 'type';
    const GRAPHQL_PATH_FIELD = 'graphqlPath';
    const ARGUMENTS_FIELD = 'args';

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
    public function isEscaped()
    {
        return $this->get(self::ESCAPED_FIELD, true);
    }

    /**
     * @inheritDoc
     */
    public function setIsEscaped($isEscaped = true)
    {
        $this->set(self::EXPRESSION_FIELD, $isEscaped);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGraphQLPath()
    {
        return $this->get(self::GRAPHQL_PATH_FIELD, (object)[]);
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
    public function getArguments()
    {
        return $this->get(self::ARGUMENTS_FIELD, (object)[]);
    }

    /**
     * @inheritDoc
     */
    public function setArguments(array $arguments)
    {
        $this->set(self::ARGUMENTS_FIELD, $arguments);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addArgument(DynamicDataHelperArgumentInterface $argument)
    {
        $args = (array)$this->getArguments();
        $args[$argument->getName()] = $argument;
        $this->setArguments($args);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD, 'scalar');
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }
}