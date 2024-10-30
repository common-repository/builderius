<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Provider;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;

class CompositeBuilderiusTemplateRuleApplicantsProvider implements BuilderiusTemplateRuleApplicantsProviderInterface
{
    /**
     * @var BuilderiusTemplateRuleApplicantsProviderInterface[]
     */
    private $providers = [];

    /**
     * @param BuilderiusTemplateRuleApplicantsProviderInterface $provider
     * @return $this
     */
    public function addProvider(BuilderiusTemplateRuleApplicantsProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getApplicants($rule, $argument, $operator, $withData = false)
    {
        $applicants = [];
        foreach ($this->providers as $provider) {
            if ($provider->isApplicable($rule, $argument, $operator)) {
                foreach ($provider->getApplicants($rule, $argument, $operator, $withData) as $k => $applicant) {
                    if (!in_array($applicant, $applicants)) {
                        $applicants[$k] = $applicant;
                    }
                }
            }
        }

        return $applicants;
    }

    /**
     * @inheritDoc
     */
    public function getChangeSetApplicants(
        BuilderiusTemplateApplicantChangeSetInterface $changeset,
        $rule,
        $argument,
        $operator,
        $withData = false
    ) {
        $applicants = [];
        foreach ($this->providers as $provider) {
            if ($provider->isApplicable($rule, $argument, $operator)) {
                foreach ($provider->getChangesetApplicants($changeset, $rule, $argument, $operator, $withData) as $k => $applicant) {
                    if (!in_array($applicant, $applicants)) {
                        $applicants[$k] = $applicant;
                    }
                }
            }
        }

        return $applicants;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($rule, $argument, $operator)
    {
        return true;
    }
}