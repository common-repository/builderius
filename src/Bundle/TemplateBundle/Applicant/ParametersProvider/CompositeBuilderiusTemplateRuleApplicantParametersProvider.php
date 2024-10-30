<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\ParametersProvider;

class CompositeBuilderiusTemplateRuleApplicantParametersProvider implements BuilderiusTemplateRuleApplicantParametersProviderInterface
{
    /**
     * @var BuilderiusTemplateRuleApplicantParametersProviderInterface[]
     */
    private $providers = [];

    /**
     * @param BuilderiusTemplateRuleApplicantParametersProviderInterface $provider
     * @return $this
     */
    public function addProvider(BuilderiusTemplateRuleApplicantParametersProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getApplicantParameters($rule, $argument, $operator, $withData = false)
    {
        $parameters = [];
        foreach ($this->providers as $provider) {
            if ($provider->isApplicable($rule, $argument, $operator)) {
                foreach ($provider->getApplicantParameters($rule, $argument, $operator, $withData) as $k => $parameter) {
                    if (!in_array($parameters, $parameter)) {
                        $parameters[$k] = $parameter;
                    }
                }
            }
        }

        return $parameters;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($rule, $argument, $operator)
    {
        return true;
    }
}