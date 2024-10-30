<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\ParametersProvider;

interface BuilderiusTemplateRuleApplicantParametersProviderInterface
{
    /**
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     * @param bool $withData
     * @return array
     */
    public function getApplicantParameters($rule, $argument, $operator, $withData = false);

    /**
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     * @return bool
     */
    public function isApplicable($rule, $argument, $operator);
}