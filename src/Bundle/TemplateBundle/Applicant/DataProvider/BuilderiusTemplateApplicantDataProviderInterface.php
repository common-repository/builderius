<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

interface BuilderiusTemplateApplicantDataProviderInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param array $applicantQueryVars
     * @return mixed
     */
    public function getData(array $applicantQueryVars = []);
}