<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Applicant;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantInterface;

interface BuilderiusTemplateApplicantsProviderInterface
{
    /**
     * @param array|null $applyRulesConfig
     * @return BuilderiusTemplateApplicantInterface[]
     */
    public function getApplicants(array $applyRulesConfig = null);
}