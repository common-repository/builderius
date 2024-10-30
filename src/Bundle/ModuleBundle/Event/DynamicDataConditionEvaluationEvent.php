<?php

namespace Builderius\Bundle\ModuleBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class DynamicDataConditionEvaluationEvent extends Event
{
    /**
     * @var string
     */
    private $function;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param array $config
     */
    public function __construct($function, array $arguments)
    {
        $this->function = $function;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}