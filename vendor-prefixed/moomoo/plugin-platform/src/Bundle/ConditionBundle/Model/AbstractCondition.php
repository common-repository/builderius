<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
abstract class AbstractCondition extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface
{
    const NAME_FIELD = 'name';
    const DESCRIPTION_FIELD = 'description';
    const DEPEND_ON_CONDITIONS = 'depend_on_conditions';
    const ARGUMENTS_FIELD = 'arguments';
    const LAZY_FIELD = 'lazy';
    /**
     * @var bool
     */
    protected $validResult = \true;
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
    public function isLazy()
    {
        return $this->get(self::LAZY_FIELD, \false);
    }
    /**
     * @inheritDoc
     */
    public function setLazy($lazy = \false)
    {
        $this->set(self::LAZY_FIELD, $lazy);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->get(self::DESCRIPTION_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->set(self::DESCRIPTION_FIELD, $description);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function addDependOnCondition(\Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface $condition)
    {
        if ($condition->isLazy() !== $this->isLazy()) {
            if ($this->isLazy() === \true) {
                throw new \Exception('Lazy Condition can be dependent only on Lazy Condition');
            } else {
                throw new \Exception('Not Lazy Condition can be dependent only on Not Lazy Condition');
            }
        }
        $conditions = $this->get(self::DEPEND_ON_CONDITIONS, []);
        $conditions[$condition->getName()] = $condition;
        $this->set(self::DEPEND_ON_CONDITIONS, $conditions);
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function setDependOnConditions(array $conditions)
    {
        $this->set(self::DEPEND_ON_CONDITIONS, []);
        foreach ($conditions as $condition) {
            $this->addDependOnCondition($condition);
        }
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getDependOnConditions()
    {
        return $this->get(self::DEPEND_ON_CONDITIONS, []);
    }
    /**
     * @param bool $result
     * @return $this
     */
    public function setValidResult($result)
    {
        $this->validResult = $result;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function evaluate()
    {
        if (!empty($this->getDependOnConditions())) {
            foreach ($this->getDependOnConditions() as $condition) {
                if ($condition->evaluate() === \false) {
                    return \false;
                }
            }
        }
        return $this->validResult === (bool) $this->getResult();
    }
    /**
     * @return bool
     */
    protected abstract function getResult();
}
