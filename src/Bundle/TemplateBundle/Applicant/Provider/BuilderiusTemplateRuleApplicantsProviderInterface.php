<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Provider;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantInterface;

interface BuilderiusTemplateRuleApplicantsProviderInterface
{
    /**
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     * @param bool $withData
     * @return BuilderiusTemplateApplicantInterface[]
     */
    public function getApplicants($rule, $argument, $operator, $withData = false);

    /**
     * @param BuilderiusTemplateApplicantChangeSetInterface $changeset
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     * @param bool $withData
     * @return BuilderiusTemplateApplicantInterface[]
     */
    public function getChangeSetApplicants(
        BuilderiusTemplateApplicantChangeSetInterface $changeset,
        $rule,
        $argument,
        $operator,
        $withData = false
    );

    /**
     * @param string $rule
     * @param mixed $argument
     * @param string $operator
     * @return bool
     */
    public function isApplicable($rule, $argument, $operator);
}