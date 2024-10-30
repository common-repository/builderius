<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

class CompositeBuilderiusTemplateApplicantDataProvider implements BuilderiusTemplateApplicantDataProviderInterface
{
    /**
     * @var BuilderiusTemplateApplicantDataProviderInterface[]
     */
    private $providers = [];

    /**
     * @param BuilderiusTemplateApplicantDataProviderInterface $provider
     * @return $this
     */
    public function addProvider(BuilderiusTemplateApplicantDataProviderInterface $provider)
    {
        $this->providers[$provider->getType()] = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getData(array $applicantQueryVars = [])
    {
        $data = [];
        foreach ($this->providers as $type => $provider) {
            $data[$type] = $provider->getData($applicantQueryVars);
        }

        return $data;
    }
}