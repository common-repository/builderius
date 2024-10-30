<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

class BodyClassesBuilderiusTemplateApplicantDataProvider implements BuilderiusTemplateApplicantDataProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'body_classes';
    }

    /**
     * @inheritDoc
     */
    public function getData(array $applicantQueryVars = [])
    {
        return get_body_class();
    }
}