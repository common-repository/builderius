<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Applicant;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantInterface;

interface BuilderiusTemplateChangeSetApplicantsProviderInterface
{
    /**
     * @param BuilderiusTemplateApplicantChangeSetInterface $changeset
     * @param array|null $applyRulesConfig
     * @return BuilderiusTemplateApplicantInterface[]
     */
    public function getChangeSetApplicants(
        BuilderiusTemplateApplicantChangeSetInterface $changeset,
        array $applyRulesConfig = null
    );
}