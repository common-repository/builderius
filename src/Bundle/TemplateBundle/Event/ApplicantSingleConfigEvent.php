<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class ApplicantSingleConfigEvent extends Event
{
    /**
     * @var array
     */
    private $applicants;

    /**
     * @var array
     */
    private $applyRulesConfig;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var mixed
     */
    private $argument;

    /**
     * @var string
     */
    private $operator;

    /**
     * @param array $applicants
     * @param array $applyRulesConfig
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     */
    public function __construct(
        array $applicants,
        array $applyRulesConfig,
        $rule,
        $argument,
        $operator
    ) {
        $this->applicants = $applicants;
        $this->applyRulesConfig = $applyRulesConfig;
        $this->rule = $rule;
        $this->argument = $argument;
        $this->operator = $operator;
    }

    /**
     * @return array
     */
    public function getApplicants(): array
    {
        return $this->applicants;
    }

    /**
     * @param array $applicants
     * @return $this
     */
    public function setApplicants(array $applicants)
    {
        $this->applicants = $applicants;

        return $this;
    }

    /**
     * @return array
     */
    public function getApplyRulesConfig()
    {
        return $this->applyRulesConfig;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return mixed
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}